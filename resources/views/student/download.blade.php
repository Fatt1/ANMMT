@extends('layouts.app')
@section('title', 'Download Documents')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Path Traversal Lab - Vulnerable vs Fixed</h1>
    <p class="text-gray-500 mt-1">Compare two endpoints side by side: one is intentionally vulnerable, one blocks traversal attacks.</p>
</div>

<div class="bg-blue-50 border border-blue-300 rounded-xl p-4 mb-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🔀</span>
        <div>
            <p class="font-semibold text-blue-800">Mode Switch</p>
            <p class="text-sm text-blue-700">Toggle between attack demo and fixed demo.</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="toggleMode('vulnerable')" id="btn-vulnerable"
                class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition mode-btn"
                data-mode="vulnerable">
            🚨 Vulnerable Mode
        </button>
        <button onclick="toggleMode('secure')" id="btn-secure"
                class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition mode-btn"
                data-mode="secure">
            ✅ Secure Mode
        </button>
    </div>
</div>

<div id="vulnerable-mode" class="mode-content">
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-red-500">
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="font-semibold text-red-800">Vulnerable Endpoint (Attack Works)</p>
            <p class="text-sm text-red-700 mt-1">
                <code class="bg-red-100 px-1 rounded font-mono">/download/file</code> concatenates raw
                <code class="bg-red-100 px-1 rounded font-mono">?file=</code> directly into server path.
            </p>
        </div>

        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Quick Payloads</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-6">
            <a href="/download/file?file=../../.env" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../.env</p>
                    <p class="text-xs text-gray-400 mt-0.5">Read APP_KEY and DB credentials</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>
            <a href="/download/file?file=../../config/database.php" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-red-700">../../config/database.php</p>
                    <p class="text-xs text-gray-400 mt-0.5">Read DB config file</p>
                </div>
                <span class="text-red-500 text-lg">→</span>
            </a>
        </div>

        <form method="GET" action="/download/file" class="border-t border-gray-100 pt-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Manual Attack Input</label>
            <input type="text" name="file"
                   class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                   placeholder="../../.env">
            <button type="submit"
                    class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg transition">
                Download via Vulnerable Endpoint
            </button>
        </form>
    </div>
</div>

<div id="secure-mode" class="mode-content hidden">
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-emerald-500">
        <div class="bg-emerald-50 border border-emerald-300 rounded-lg p-4 mb-6">
            <p class="font-semibold text-emerald-800">Secure Endpoint (Attack Blocked)</p>
            <p class="text-sm text-emerald-700 mt-1">
                <code class="bg-emerald-100 px-1 rounded font-mono">/download/file/secure</code> uses
                <code class="bg-emerald-100 px-1 rounded font-mono">realpath</code> and base-path check.
            </p>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <p class="text-sm font-medium text-gray-700">Defense logic</p>
            <ul class="text-sm text-gray-600 mt-2 space-y-1 list-disc list-inside">
                <li>Resolve absolute path with <span class="font-mono">realpath</span></li>
                <li>Ensure resolved file stays under <span class="font-mono">storage/documents</span></li>
                <li>Reject traversal attempts with HTTP 403</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-6">
            <a href="/download/file/secure?file=syllabus.pdf" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-emerald-700">syllabus.pdf</p>
                    <p class="text-xs text-gray-400 mt-0.5">Valid file - should download</p>
                </div>
                <span class="text-emerald-500 text-lg">→</span>
            </a>
            <a href="/download/file/secure?file=../../.env" target="_blank"
               class="flex items-center justify-between bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-lg px-4 py-3 transition group">
                <div>
                    <p class="text-sm font-mono font-medium text-gray-800 group-hover:text-emerald-700">../../.env</p>
                    <p class="text-xs text-gray-400 mt-0.5">Traversal payload - should be blocked (403)</p>
                </div>
                <span class="text-emerald-500 text-lg">→</span>
            </a>
        </div>

        <form method="GET" action="/download/file/secure" class="border-t border-gray-100 pt-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Manual Secure Check</label>
            <input type="text" name="file"
                   class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
                   placeholder="syllabus.pdf">
            <button type="submit"
                    class="w-full mt-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-lg transition">
                Download via Secure Endpoint
            </button>
        </form>
    </div>
</div>

<script>
    function toggleMode(mode) {
        const vulnerable = document.getElementById('vulnerable-mode');
        const secure = document.getElementById('secure-mode');
        const btnVuln = document.getElementById('btn-vulnerable');
        const btnSecure = document.getElementById('btn-secure');

        if (mode === 'vulnerable') {
            vulnerable.classList.remove('hidden');
            secure.classList.add('hidden');

            btnVuln.classList.remove('bg-gray-300', 'text-gray-700');
            btnVuln.classList.add('bg-red-500', 'text-white');

            btnSecure.classList.remove('bg-green-500', 'text-white');
            btnSecure.classList.add('bg-gray-300', 'text-gray-700');
        } else {
            vulnerable.classList.add('hidden');
            secure.classList.remove('hidden');

            btnSecure.classList.remove('bg-gray-300', 'text-gray-700');
            btnSecure.classList.add('bg-green-500', 'text-white');

            btnVuln.classList.remove('bg-red-500', 'text-white');
            btnVuln.classList.add('bg-gray-300', 'text-gray-700');
        }
    }
</script>
@endsection
