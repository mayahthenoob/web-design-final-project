import sql from "@/app/api/utils/sql";
import { auth } from "@/auth";

export async function GET() {
  try {
    const session = await auth();
    if (!session || !session.user?.id) {
      return Response.json({ error: "Unauthorized" }, { status: 401 });
    }

    const userId = session.user.id;
    const rows = await sql`
      SELECT id, name, email, image, username, role, school, country, avatar_url, total_score, created_at 
      FROM auth_users 
      WHERE id = ${userId} 
      LIMIT 1
    `;
    const user = rows?.[0] || null;
    return Response.json({ user });
  } catch (err) {
    console.error("GET /api/profile error", err);
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
    const { username, role, school, country, avatar_url } = body || {};

    const setClauses = [];
    const values = [];

    if (typeof username === "string" && username.trim().length > 0) {
      setClauses.push("username = $" + (values.length + 1));
      values.push(username.trim());
    }

    if (typeof role === "string" && role.trim().length > 0) {
      setClauses.push("role = $" + (values.length + 1));
      values.push(role.trim());
    }

    if (typeof school === "string" && school.trim().length > 0) {
      setClauses.push("school = $" + (values.length + 1));
      values.push(school.trim());
    }

    if (typeof country === "string" && country.trim().length > 0) {
      setClauses.push("country = $" + (values.length + 1));
      values.push(country.trim());
    }

    if (typeof avatar_url === "string" && avatar_url.trim().length > 0) {
      setClauses.push("avatar_url = $" + (values.length + 1));
      values.push(avatar_url.trim());
    }

    if (setClauses.length === 0) {
      return Response.json(
        { error: "No valid fields to update" },
        { status: 400 },
      );
    }

    const finalQuery = `UPDATE auth_users SET ${setClauses.join(", ")} WHERE id = $${values.length + 1} RETURNING id, name, email, image, username, role, school, country, avatar_url, total_score, created_at`;

    const result = await sql(finalQuery, [...values, userId]);
    const updated = result?.[0] || null;

    return Response.json({ user: updated });
  } catch (err) {
    console.error("PUT /api/profile error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
