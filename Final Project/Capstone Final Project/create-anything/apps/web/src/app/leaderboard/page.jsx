import { useState, useEffect } from "react";
import useUser from "@/utils/useUser";

function MainComponent() {
  const { data: user, loading: userLoading } = useUser();
  const [leaderboard, setLeaderboard] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("global");
  const [selectedSubject, setSelectedSubject] = useState("");

  const subjects = [
    "Mathematics",
    "Science",
    "English",
    "History",
    "Geography",
    "Physics",
    "Chemistry",
    "Biology",
  ];

  useEffect(() => {
    const fetchLeaderboard = async () => {
      try {
        setLoading(true);
        let url = `/api/leaderboard?type=${activeTab}&limit=50`;

        if (activeTab === "subject" && selectedSubject) {
          url += `&subject=${encodeURIComponent(selectedSubject)}`;
        }

        const response = await fetch(url);
        if (response.ok) {
          const data = await response.json();
          setLeaderboard(data.leaderboard);
        }
      } catch (error) {
        console.error("Error fetching leaderboard:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchLeaderboard();
  }, [activeTab, selectedSubject]);

  if (userLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  const getRankIcon = (rank) => {
    if (rank === 1) return "🥇";
    if (rank === 2) return "🥈";
    if (rank === 3) return "🥉";
    return `#${rank}`;
  };

  const getRankColor = (rank) => {
    if (rank === 1) return "bg-yellow-100 text-yellow-800";
    if (rank === 2) return "bg-gray-100 text-gray-800";
    if (rank === 3) return "bg-orange-100 text-orange-800";
    return "bg-blue-100 text-blue-800";
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation */}
      <nav className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <a href="/dashboard" className="text-2xl font-bold text-blue-600">
                EduQuiz
              </a>
            </div>
            <div className="flex items-center space-x-4">
              <a
                href="/dashboard"
                className="text-gray-700 hover:text-blue-600"
              >
                Dashboard
              </a>
              {user && (
                <a
                  href="/account/logout"
                  className="text-gray-500 hover:text-gray-700"
                >
                  Sign Out
                </a>
              )}
            </div>
          </div>
        </div>
      </nav>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Leaderboards
          </h1>
          <p className="text-gray-600">
            See how you rank against students and schools worldwide
          </p>
        </div>

        {/* Tabs */}
        <div className="mb-8">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8">
              <button
                onClick={() => setActiveTab("global")}
                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                  activeTab === "global"
                    ? "border-blue-500 text-blue-600"
                    : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                }`}
              >
                Global Students
              </button>
              <button
                onClick={() => setActiveTab("school")}
                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                  activeTab === "school"
                    ? "border-blue-500 text-blue-600"
                    : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                }`}
              >
                Schools
              </button>
              <button
                onClick={() => setActiveTab("subject")}
                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                  activeTab === "subject"
                    ? "border-blue-500 text-blue-600"
                    : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                }`}
              >
                By Subject
              </button>
            </nav>
          </div>
        </div>

        {/* Subject Filter */}
        {activeTab === "subject" && (
          <div className="mb-6">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Select Subject
            </label>
            <select
              value={selectedSubject}
              onChange={(e) => setSelectedSubject(e.target.value)}
              className="block w-full max-w-xs px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Choose a subject...</option>
              {subjects.map((subject) => (
                <option key={subject} value={subject}>
                  {subject}
                </option>
              ))}
            </select>
          </div>
        )}

        {/* Leaderboard Content */}
        <div className="bg-white shadow rounded-lg">
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-xl font-semibold text-gray-900">
              {activeTab === "global" && "Top Students Worldwide"}
              {activeTab === "school" && "Top Schools"}
              {activeTab === "subject" &&
                selectedSubject &&
                `Top Students in ${selectedSubject}`}
              {activeTab === "subject" &&
                !selectedSubject &&
                "Select a Subject"}
            </h2>
          </div>

          {loading ? (
            <div className="p-8 text-center">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p className="text-gray-600">Loading leaderboard...</p>
            </div>
          ) : leaderboard.length === 0 ? (
            <div className="p-8 text-center">
              <svg
                className="w-12 h-12 text-gray-400 mx-auto mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                />
              </svg>
              <p className="text-gray-600">No data available yet</p>
              <p className="text-sm text-gray-500 mt-2">
                {activeTab === "subject" && !selectedSubject
                  ? "Please select a subject to view rankings"
                  : "Be the first to take a quiz and appear on the leaderboard!"}
              </p>
            </div>
          ) : (
            <div className="overflow-hidden">
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rank
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {activeTab === "school" ? "School" : "Student"}
                      </th>
                      {activeTab !== "school" && (
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          School
                        </th>
                      )}
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {activeTab === "school" ? "Country" : "Location"}
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Score
                      </th>
                      {activeTab === "school" && (
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Students
                        </th>
                      )}
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {leaderboard.map((entry, index) => {
                      const rank = entry.rank_position || index + 1;
                      const isCurrentUser = user && entry.id === user.id;

                      return (
                        <tr
                          key={entry.id}
                          className={`${isCurrentUser ? "bg-blue-50 border-l-4 border-blue-500" : "hover:bg-gray-50"}`}
                        >
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div
                              className={`inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold ${getRankColor(rank)}`}
                            >
                              {getRankIcon(rank)}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center">
                              {activeTab !== "school" && entry.avatar_url ? (
                                <img
                                  src={entry.avatar_url}
                                  alt="Avatar"
                                  className="w-10 h-10 rounded-full mr-3"
                                />
                              ) : activeTab !== "school" ? (
                                <div className="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                  <span className="text-gray-600 text-sm font-medium">
                                    {entry.username?.[0]?.toUpperCase() ||
                                      entry.name?.[0]?.toUpperCase() ||
                                      "?"}
                                  </span>
                                </div>
                              ) : null}
                              <div>
                                <div className="text-sm font-medium text-gray-900">
                                  {activeTab === "school"
                                    ? entry.name
                                    : entry.username || entry.name}
                                  {isCurrentUser && (
                                    <span className="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                      You
                                    </span>
                                  )}
                                </div>
                              </div>
                            </div>
                          </td>
                          {activeTab !== "school" && (
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {entry.school || "Not specified"}
                            </td>
                          )}
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {entry.country || "Not specified"}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm font-medium text-gray-900">
                              {entry.total_score?.toLocaleString() || 0}
                            </div>
                          </td>
                          {activeTab === "school" && (
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {entry.student_count || 0}
                            </td>
                          )}
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>

        {/* Call to Action */}
        {user && (
          <div className="mt-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg">
            <div className="p-6 text-white text-center">
              <h2 className="text-xl font-semibold mb-2">
                Want to climb the leaderboard?
              </h2>
              <p className="mb-4">
                Take more quizzes to improve your ranking and compete with
                students worldwide!
              </p>
              <a
                href="/quiz"
                className="bg-white text-blue-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors"
              >
                Take a Quiz
              </a>
            </div>
          </div>
        )}

        {!user && (
          <div className="mt-8 bg-gray-100 rounded-lg">
            <div className="p-6 text-center">
              <h2 className="text-xl font-semibold text-gray-900 mb-2">
                Join the Competition!
              </h2>
              <p className="text-gray-600 mb-4">
                Sign up to start taking quizzes and see your name on the
                leaderboard.
              </p>
              <a
                href="/account/signup"
                className="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors"
              >
                Get Started
              </a>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

export default MainComponent;
