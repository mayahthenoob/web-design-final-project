import useAuth from "@/utils/useAuth";

function MainComponent() {
  const { signOut } = useAuth();

  const handleSignOut = async () => {
    await signOut({
      callbackUrl: "/",
      redirect: true,
    });
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="bg-white rounded-2xl shadow-xl p-8 text-center">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">Sign Out</h1>
          <p className="text-gray-600 mb-8">
            Are you sure you want to sign out of your account?
          </p>

          <button
            onClick={handleSignOut}
            className="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors"
          >
            Sign Out
          </button>

          <a
            href="/dashboard"
            className="block mt-4 text-blue-600 hover:text-blue-700 font-medium"
          >
            Cancel
          </a>
        </div>
      </div>
    </div>
  );
}

export default MainComponent;
