@extends('layouts.app')
@section('title', 'OOB SQLi Lab')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Out-of-band SQL Injection Lab</h1>
        <p class="text-gray-500 mt-1">Trigger external DB interaction via user-controlled SQL construction.</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-pink-100">
        <h2 class="text-lg font-semibold text-pink-700 mb-4">OOB Channel Probe</h2>
        <form method="GET" action="/sqli/oob/test" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">External channel / host token</label>
            <input
                type="text"
                name="channel"
                value="{{ $channel ?? 'attacker.lab' }}"
                class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-pink-400"
                placeholder="attacker.lab"
            >
            <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg transition">Run OOB Test</button>
        </form>

        <div class="mt-4 bg-gray-50 border rounded-lg p-3 text-xs font-mono text-pink-700">
            Example target path built by SQL: \\attacker.lab\share\probe.txt
        </div>
    </div>

    @isset($sql)
        <div class="bg-white rounded-xl shadow p-6 border border-gray-100 space-y-3">
            <h3 class="text-base font-semibold text-gray-800">Execution Result</h3>
            <div class="bg-gray-900 text-green-300 rounded-lg p-3 font-mono text-xs overflow-x-auto">{{ $sql }}</div>

            @if($dbError)
                <div class="bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-sm">
                    Database error: {{ $dbError }}
                </div>
            @else
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-lg p-3 text-sm">
                    Query executed. If DB host can resolve/reach the external channel, this can become an OOB data path.
                </div>
            @endif

            @if(!empty($rows))
                <pre class="bg-gray-100 rounded-lg p-3 text-xs overflow-x-auto">{{ print_r($rows, true) }}</pre>
            @endif
        </div>
    @endisset

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-900">
        <p class="font-semibold mb-1">Fix guidance (for remediation phase)</p>
        <p>Disallow dangerous DB functions (LOAD_FILE, INTO OUTFILE), use parameterized statements, and enforce strict allow-list validation for external resource identifiers.</p>
    </div>
</div>
@endsection
