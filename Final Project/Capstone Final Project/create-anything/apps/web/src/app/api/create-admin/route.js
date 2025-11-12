import sql from "@/app/api/utils/sql";
import { auth } from "@/auth";

export async function POST(request) {
  try {
    const body = await request.json();
    const { userId } = body;

    if (!userId) {
      return Response.json({ error: "User ID is required" }, { status: 400 });
    }

    // Check if user exists
    const user = await sql`
      SELECT id, role FROM auth_users WHERE id = ${userId} LIMIT 1
    `;

    if (user.length === 0) {
      return Response.json({ error: "User not found" }, { status: 404 });
    }

    // Update user role to admin
    const result = await sql`
      UPDATE auth_users 
      SET role = 'admin' 
      WHERE id = ${userId}
      RETURNING id, username, name, email, role
    `;

    return Response.json({
      success: true,
      message: "User promoted to admin successfully",
      user: result[0],
    });
  } catch (err) {
    console.error("POST /api/create-admin error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}

export async function GET() {
  return Response.json({
    message:
      "This endpoint is for creating the first admin user. Send a POST request with userId to promote a user to admin.",
    instructions: [
      "1. Sign up for an account first",
      "2. Get your user ID from your profile",
      "3. Send a POST request to this endpoint with your userId",
      "4. Delete this route after creating your admin account",
    ],
  });
}
