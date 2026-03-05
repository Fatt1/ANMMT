<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="text" name="email"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                       placeholder="student@lab.local">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Login
            </button>
        </form>

        <p class="text-sm text-center text-gray-500 mt-4">
            Don't have an account?
            <a href="/register" class="text-blue-600 hover:underline">Register</a>
        </p>
    </div>
</body>
</html>
