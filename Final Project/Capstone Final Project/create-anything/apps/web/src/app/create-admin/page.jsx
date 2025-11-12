import { useState, useEffect } from "react";
import useUser from "@/utils/useUser";

function MainComponent() {
  const { data: user, loading: userLoading } = useUser();
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState(null);
  const [userProfile, setUserProfile] = useState(null);

  useEffect(() => {
    const fetchProfile = async () => {
      if (!user) return;

      try {
        const response = await fetch("/api/profile");
        if (response.ok) {
          const data = await response.json();
          setUserProfile(data.user);
        }
      } catch (error) {
        console.error("Error fetching profile:", error);
      }
    };

    if (!userLoading && user) {
      fetchProfile();
    }
  }, [user, userLoading]);

  const handleCreateAdmin = async () => {
    if (!user) {
      setError("You must be signed in to create an admin account");
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const response = await fetch("/api/create-admin", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ userId: user.id }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || "Failed to create admin");
      }

      setSuccess(true);

      // Refresh profile to show new role
      const profileResponse = await fetch("/api/profile");
      if (profileResponse.ok) {
        const profileData = await profileResponse.json();
        setUserProfile(profileData.user);
      }
    } catch (err) {
      console.error("Create admin error:", err);
      setError(err.message || "Failed to create admin");
    } finally {
      setLoading(false);
    }
  };

  if (userLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation */}
      <nav className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <a
                href={user ? "/dashboard" : "/"}
                className="text-2xl font-bold text-blue-600"
              >
                EduQuiz
              </a>
            </div>
            <div className="flex items-center space-x-4">
              {user ? (
                <>
                  <a
                    href="/dashboard"
                    className="text-gray-700 hover:text-blue-600"
                  >
                    Dashboard
                  </a>
                  <a
                    href="/account/logout"
                    className="text-gray-500 hover:text-gray-700"
                  >
                    Sign Out
                  </a>
                </>
              ) : (
                <>
                  <a
                    href="/account/signin"
                    className="text-gray-700 hover:text-blue-600"
                  >
                    Sign In
                  </a>
                  <a
                    href="/account/signup"
                    className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                  >
                    Get Started
                  </a>
                </>
              )}
            </div>
          </div>
        </div>
      </nav>

      <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="bg-white rounded-lg shadow p-8">
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg
                className="w-8 h-8 text-red-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                />
              </svg>
            </div>
            <h1 className="text-3xl font-bold text-gray-900 mb-2">
              Create First Admin User
            </h1>
            <p className="text-gray-600">
              This is a special route for creating the first admin user.
              <strong className="text-red-600">
                {" "}
                Delete this route after use!
              </strong>
            </p>
          </div>

          {!user ? (
            <div className="text-center">
              <p className="text-gray-600 mb-6">
                You must be signed in to create an admin account. Please sign up
                first, then return to this page.
              </p>
              <div className="space-x-4">
                <a
                  href="/account/signup"
                  className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Sign Up
                </a>
                <a
                  href="/account/signin"
                  className="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Sign In
                </a>
              </div>
            </div>
          ) : success ? (
            <div className="text-center">
              <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg
                  className="w-8 h-8 text-green-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </div>
              <h2 className="text-2xl font-bold text-gray-900 mb-2">
                Admin Account Created!
              </h2>
              <p className="text-gray-600 mb-6">
                Your account has been successfully promoted to admin. You now
                have access to the admin panel.
              </p>
              <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div className="flex">
                  <svg
                    className="w-5 h-5 text-yellow-400 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fillRule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clipRule="evenodd"
                    />
                  </svg>
                  <div>
                    <h3 className="text-sm font-medium text-yellow-800">
                      Important Security Notice
                    </h3>
                    <p className="text-sm text-yellow-700 mt-1">
                      Please delete the file{" "}
                      <code>/apps/web/src/app/create-admin/page.jsx</code> and
                      <code>/apps/web/src/app/api/create-admin/route.js</code>{" "}
                      immediately for security reasons.
                    </p>
                  </div>
                </div>
              </div>
              <div className="space-x-4">
                <a
                  href="/admin"
                  className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Go to Admin Panel
                </a>
                <a
                  href="/dashboard"
                  className="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Go to Dashboard
                </a>
              </div>
            </div>
          ) : (
            <div>
              <div className="mb-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">
                  Current User Information
                </h2>
                <div className="bg-gray-50 rounded-lg p-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <p className="text-sm font-medium text-gray-600">
                        User ID
                      </p>
                      <p className="text-gray-900">{user.id}</p>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-gray-600">Email</p>
                      <p className="text-gray-900">{user.email}</p>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-gray-600">
                        Username
                      </p>
                      <p className="text-gray-900">
                        {userProfile?.username || "Not set"}
                      </p>
                    </div>
                    <div>
                      <p className="text-sm font-medium text-gray-600">
                        Current Role
                      </p>
                      <p className="text-gray-900">
                        {userProfile?.role || "student"}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div className="flex">
                  <svg
                    className="w-5 h-5 text-red-400 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fillRule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clipRule="evenodd"
                    />
                  </svg>
                  <div>
                    <h3 className="text-sm font-medium text-red-800">
                      Security Warning
                    </h3>
                    <p className="text-sm text-red-700 mt-1">
                      This route should only be used once to create the first
                      admin user. After creating your admin account, delete this
                      route immediately to prevent unauthorized access.
                    </p>
                  </div>
                </div>
              </div>

              {error && (
                <div className="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm mb-6">
                  {error}
                </div>
              )}

              <div className="text-center">
                <button
                  onClick={handleCreateAdmin}
                  disabled={loading || userProfile?.role === "admin"}
                  className="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {loading
                    ? "Creating Admin..."
                    : userProfile?.role === "admin"
                      ? "Already Admin"
                      : "Promote to Admin"}
                </button>

                {userProfile?.role === "admin" && (
                  <p className="text-green-600 text-sm mt-2">
                    You are already an admin user!
                  </p>
                )}
              </div>
            </div>
          )}

          <div className="mt-8 pt-6 border-t border-gray-200">
            <h3 className="text-sm font-medium text-gray-900 mb-2">
              Instructions:
            </h3>
            <ol className="text-sm text-gray-600 space-y-1">
              <li>1. Sign up for an account if you haven't already</li>
              <li>2. Click "Promote to Admin" to make your account an admin</li>
              <li>3. Access the admin panel from your dashboard</li>
              <li>
                4.{" "}
                <strong className="text-red-600">
                  Delete this route immediately after use
                </strong>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  );
}

export default MainComponent;
