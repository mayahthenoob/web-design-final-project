import sql from "@/app/api/utils/sql";
import { auth } from "@/auth";

export async function GET(request) {
  try {
    const session = await auth();
    if (!session || !session.user?.id) {
      return Response.json({ error: "Unauthorized" }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const isAdmin = searchParams.get("admin") === "true";
    const userId = session.user.id;

    // Check if user is admin (you might want to add role checking here)
    const user = await sql`
      SELECT role FROM auth_users WHERE id = ${userId} LIMIT 1
    `;

    if (isAdmin && user[0]?.role !== "admin") {
      return Response.json({ error: "Admin access required" }, { status: 403 });
    }

    let query;
    let params = [];

    if (isAdmin) {
      // Admin can see all feedback
      query = `
        SELECT 
          cf.*,
          u.username,
          u.name,
          u.email
        FROM contact_feedback cf
        LEFT JOIN auth_users u ON cf.user_id = u.id
        ORDER BY cf.created_at DESC
      `;
    } else {
      // Users can only see their own feedback
      query = `
        SELECT * FROM contact_feedback 
        WHERE user_id = $1
        ORDER BY created_at DESC
      `;
      params = [userId];
    }

    const results = await sql(query, params);
    return Response.json({ feedback: results });
  } catch (err) {
    console.error("GET /api/contact error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}

export async function POST(request) {
  try {
    const session = await auth();
    const body = await request.json();
    const { subject, message, feedback_type = "general" } = body;

    if (!subject || !message) {
      return Response.json(
        { error: "Subject and message are required" },
        { status: 400 },
      );
    }

    const userId = session?.user?.id || null;

    const result = await sql`
      INSERT INTO contact_feedback (user_id, subject, message, feedback_type)
      VALUES (${userId}, ${subject}, ${message}, ${feedback_type})
      RETURNING id, subject, message, feedback_type, status, created_at
    `;

    return Response.json({
      success: true,
      feedback: result[0],
    });
  } catch (err) {
    console.error("POST /api/contact error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}

export async function PUT(request) {
  try {
    const session = await auth();
    if (!session || !session.user?.id) {
      return Response.json({ error: "Unauthorized" }, { status: 401 });
    }

    const userId = session.user.id;
    const body = await request.json();
    const { id, status, admin_response } = body;

    if (!id) {
      return Response.json(
        { error: "Feedback ID is required" },
        { status: 400 },
      );
    }

    // Check if user is admin
    const user = await sql`
      SELECT role FROM auth_users WHERE id = ${userId} LIMIT 1
    `;

    if (user[0]?.role !== "admin") {
      return Response.json({ error: "Admin access required" }, { status: 403 });
    }

    const setClauses = [];
    const values = [];

    if (status) {
      setClauses.push("status = $" + (values.length + 1));
      values.push(status);
    }

    if (admin_response) {
      setClauses.push("admin_response = $" + (values.length + 1));
      values.push(admin_response);
    }

    setClauses.push("updated_at = CURRENT_TIMESTAMP");

    if (setClauses.length === 0) {
      return Response.json({ error: "No fields to update" }, { status: 400 });
    }

    const query = `
      UPDATE contact_feedback 
      SET ${setClauses.join(", ")} 
      WHERE id = $${values.length + 1}
      RETURNING *
    `;

    const result = await sql(query, [...values, id]);

    if (result.length === 0) {
      return Response.json({ error: "Feedback not found" }, { status: 404 });
    }

    return Response.json({
      success: true,
      feedback: result[0],
    });
  } catch (err) {
    console.error("PUT /api/contact error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
