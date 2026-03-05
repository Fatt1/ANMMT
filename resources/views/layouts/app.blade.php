<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CTF Lab') — Security Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Top Navbar --}}
    <header class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between shadow-lg z-10">
        <a href="/dashboard" class="flex items-center gap-3 hover:opacity-80 transition">
            <span class="text-2xl">🎓</span>
            <span class="font-bold text-lg tracking-wide">Student Management — Security Lab</span>
        </a>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-300">
                <span class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
                </span>
                <span>{{ auth()->user()->name ?? auth()->user()->email }}</span>
            </div>
            <form method="POST" action="/logout">
                @csrf
                <button class="text-sm bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded transition">Logout</button>
            </form>
        </div>
    </header>

    <div class="flex flex-1">

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-800 text-white flex flex-col shadow-xl min-h-screen">
            <div class="px-5 py-6 border-b border-gray-700">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Logged in as</p>
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? auth()->user()->email }}</p>
            </div>

            <nav class="flex-1 px-3 py-5 space-y-1">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 px-3 mb-3">Lab Modules</p>

                {{-- Dashboard --}}
                <a href="/dashboard"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->is('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                    <span class="text-lg">🏠</span>
                    <div>
                        <p class="text-sm font-medium">Dashboard</p>
                    </div>
                </a>

                <div class="border-t border-gray-700 my-2"></div>

                {{-- Module 1: SQL Injection --}}
                <a href="/"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->is('/') ? 'bg-red-700 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                    <span class="text-lg">💉</span>
                    <div>
                        <p class="text-sm font-medium">SQL Injection</p>
                        <p class="text-xs text-gray-400">OWASP A03</p>
                    </div>
                </a>

                {{-- Module 2: IDOR --}}
                <a href="/student/grades/1"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->is('student/grades*') ? 'bg-yellow-600 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                    <span class="text-lg">🔓</span>
                    <div>
                        <p class="text-sm font-medium">IDOR — Grades</p>
                        <p class="text-xs {{ request()->is('student/grades*') ? 'text-yellow-200' : 'text-gray-400' }}">OWASP A01</p>
                    </div>
                </a>

                {{-- Module 3: File Upload --}}
                <a href="/student/upload"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->is('student/upload*') ? 'bg-orange-600 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                    <span class="text-lg">📤</span>
                    <div>
                        <p class="text-sm font-medium">File Upload</p>
                        <p class="text-xs {{ request()->is('student/upload*') ? 'text-orange-200' : 'text-gray-400' }}">OWASP A04</p>
                    </div>
                </a>

                {{-- Module 4: Path Traversal --}}
                <a href="/download"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->is('download*') ? 'bg-purple-600 text-white' : 'hover:bg-gray-700 text-gray-300' }}">
                    <span class="text-lg">📂</span>
                    <div>
                        <p class="text-sm font-medium">Path Traversal</p>
                        <p class="text-xs {{ request()->is('download*') ? 'text-purple-200' : 'text-gray-400' }}">OWASP A01</p>
                    </div>
                </a>
            </nav>

            <div class="px-5 py-4 border-t border-gray-700">
                <span class="text-xs text-gray-500">CTF Security Lab · 2026</span>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-10 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</body>
</html>
