import sql from "@/app/api/utils/sql";
import { auth } from "@/auth";

export async function GET(request) {
  try {
    const { searchParams } = new URL(request.url);
    const type = searchParams.get("type") || "global"; // global, school, subject
    const subject = searchParams.get("subject");
    const limit = parseInt(searchParams.get("limit")) || 50;

    let query;
    let params = [];

    if (type === "global") {
      query = `
        SELECT 
          u.id,
          u.username,
          u.name,
          u.school,
          u.country,
          u.avatar_url,
          u.total_score,
          ROW_NUMBER() OVER (ORDER BY u.total_score DESC) as rank_position
        FROM auth_users u
        WHERE u.username IS NOT NULL AND u.total_score > 0
        ORDER BY u.total_score DESC
        LIMIT $1
      `;
      params = [limit];
    } else if (type === "school") {
      query = `
        SELECT 
          s.id,
          s.name,
          s.country,
          s.total_score,
          s.student_count,
          ROW_NUMBER() OVER (ORDER BY s.total_score DESC) as rank_position
        FROM schools s
        WHERE s.total_score > 0
        ORDER BY s.total_score DESC
        LIMIT $1
      `;
      params = [limit];
    } else if (type === "subject" && subject) {
      query = `
        SELECT 
          u.id,
          u.username,
          u.name,
          u.school,
          u.country,
          u.avatar_url,
          le.total_score,
          le.rank_position
        FROM leaderboard_entries le
        JOIN auth_users u ON le.user_id = u.id
        WHERE le.subject = $1
        ORDER BY le.total_score DESC
        LIMIT $2
      `;
      params = [subject, limit];
    }

    const results = await sql(query, params);

    return Response.json({
      leaderboard: results,
      type,
      subject: subject || null,
    });
  } catch (err) {
    console.error("GET /api/leaderboard error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}

export async function POST(request) {
  try {
    const session = await auth();
    if (!session || !session.user?.id) {
      return Response.json({ error: "Unauthorized" }, { status: 401 });
    }

    const body = await request.json();
    const { subject, score } = body;

    if (!subject || typeof score !== "number") {
      return Response.json(
        { error: "Subject and score are required" },
        { status: 400 },
      );
    }

    const userId = session.user.id;

    // Update or insert leaderboard entry
    const existingEntry = await sql`
      SELECT id, total_score FROM leaderboard_entries 
      WHERE user_id = ${userId} AND subject = ${subject}
      LIMIT 1
    `;

    if (existingEntry.length > 0) {
      // Update existing entry if new score is higher
      const currentScore = existingEntry[0].total_score;
      if (score > currentScore) {
        await sql`
          UPDATE leaderboard_entries 
          SET total_score = ${score}, last_updated = CURRENT_TIMESTAMP
          WHERE user_id = ${userId} AND subject = ${subject}
        `;
      }
    } else {
      // Create new entry
      await sql`
        INSERT INTO leaderboard_entries (user_id, subject, total_score)
        VALUES (${userId}, ${subject}, ${score})
      `;
    }

    // Update user's total score
    await sql`
      UPDATE auth_users 
      SET total_score = (
        SELECT COALESCE(SUM(total_score), 0) 
        FROM leaderboard_entries 
        WHERE user_id = ${userId}
      )
      WHERE id = ${userId}
    `;

    return Response.json({ success: true });
  } catch (err) {
    console.error("POST /api/leaderboard error", err);
    return Response.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
