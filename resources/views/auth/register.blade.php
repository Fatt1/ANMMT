<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register — Student Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Create Account</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                <p class="text-xs text-gray-400 mt-1">Minimum 8 characters.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Register
            </button>
        </form>

        <p class="text-sm text-center text-gray-500 mt-4">
            Already have an account?
            <a href="/" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
