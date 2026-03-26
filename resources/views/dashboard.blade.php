@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Lab Dashboard</h1>
    <p class="text-gray-500 mt-1">Select a vulnerability module to begin the exercise.</p>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-10">
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-t-4 border-red-400">
        <span class="text-3xl">💉</span>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Module 1</p>
            <p class="font-semibold text-gray-700 text-sm">SQL Injection</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-t-4 border-yellow-400">
        <span class="text-3xl">🔓</span>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Module 2</p>
            <p class="font-semibold text-gray-700 text-sm">IDOR</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-t-4 border-orange-400">
        <span class="text-3xl">📤</span>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Module 3</p>
            <p class="font-semibold text-gray-700 text-sm">File Upload</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-t-4 border-purple-400">
        <span class="text-3xl">📂</span>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Module 4</p>
            <p class="font-semibold text-gray-700 text-sm">Path Traversal</p>
        </div>
    </div>
</div>

{{-- Module Cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Module 1: SQL Injection --}}
    <a href="/"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-red-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">💉</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition">SQL Injection</h2>
                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-0.5 rounded-full">A03</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Bypass authentication without a valid password by injecting SQL into the email field.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-red-600 border border-gray-200">
                    ' OR '1'='1' --
                </div>
            </div>
        </div>
    </a>

    {{-- Module 1B: Blind SQLi --}}
    <a href="/sqli/blind"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-rose-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">🕶️</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-rose-600 transition">Blind SQL Injection</h2>
                    <span class="text-xs font-medium bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full">Inferential</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Exploit true/false behavior and timing differences to infer data without direct output.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-rose-700 border border-gray-200">
                    1' AND IF(1=1,SLEEP(3),0) --
                </div>
            </div>
        </div>
    </a>

    {{-- Module 1C: OOB SQLi --}}
    <a href="/sqli/oob"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-pink-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">📡</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-pink-600 transition">Out-of-band SQLi</h2>
                    <span class="text-xs font-medium bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">OOB</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Trigger external interaction from DB functions (DNS/SMB/HTTP) to exfiltrate data indirectly.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-pink-700 border border-gray-200">
                    \\attacker.lab\share\probe.txt
                </div>
            </div>
        </div>
    </a>

    {{-- Module 1D: UNION-based SQLi --}}
    <a href="/sqli/union"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-red-400 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">🧬</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition">UNION-based SQL Injection</h2>
                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-0.5 rounded-full">UNION</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Exploit product detail id parameter with UNION SELECT to pull database metadata.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-red-700 border border-gray-200">
                    /product/detail?id=-1 UNION SELECT 1,database(),999--
                </div>
            </div>
        </div>
    </a>

    {{-- Module 2: IDOR --}}
    <a href="/student/grades/1"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-yellow-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">🔓</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-yellow-600 transition">IDOR — View Grades</h2>
                    <span class="text-xs font-medium bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">A01</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Access other students' grade records by manipulating the ID parameter in the URL.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-yellow-700 border border-gray-200">
                    /student/grades/<span class="font-bold text-red-500">1</span> → /student/grades/<span class="font-bold text-red-500">2</span>
                </div>
            </div>
        </div>
    </a>

    {{-- Module 3: Unrestricted File Upload --}}
    <a href="/student/upload"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-orange-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">📤</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-orange-600 transition">Unrestricted File Upload</h2>
                    <span class="text-xs font-medium bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">A04</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Upload a PHP web shell and execute arbitrary commands on the server.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-orange-700 border border-gray-200">
                    shell.php → /public/uploads/shell.php
                </div>
            </div>
        </div>
    </a>

    {{-- Module 4: Path Traversal --}}
    <a href="/download"
       class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border-l-4 border-purple-500 group">
        <div class="flex items-start gap-4">
            <div class="text-3xl mt-1">📂</div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition">Path Traversal — Download</h2>
                    <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">A01</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">Read arbitrary server files such as <code class="bg-gray-100 px-1 rounded">.env</code> by traversing directories.</p>
                <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono text-purple-700 border border-gray-200">
                    ?file=../../.env
                </div>
            </div>
        </div>
    </a>

</div>
@endsection

