---
name: create-vuln-download
description: 'Creates the intentionally vulnerable Document Download module (Path Traversal / OWASP A01) for the CTF security lab. Use when asked to scaffold the download controller, download route, or document download page. Produces: DownloadController that concatenates a raw ?file= query parameter onto storage_path("documents") with NO sanitization, GET /download route, and a Tailwind download blade. Marks the vulnerable line with // [VULNERABLE HERE]. Do NOT use for production code.'
argument-hint: 'Optional extras, e.g. "list available documents" or "use file_get_contents instead of response()->download()"'
---

# Create Vulnerable Document Download Module (Path Traversal)

## When to Use
- "Create the download module"
- "Scaffold the DownloadController"
- "Build the document download page for the lab"
- "Implement path traversal on the download endpoint"
- Any request to generate a file download flow for the intentionally vulnerable student management app

## Vulnerability Target
**OWASP A01:2021 – Broken Access Control (Path Traversal)**
The `?file=` query parameter is concatenated directly onto `storage_path('documents')` without stripping `../` sequences. An attacker can escape the intended directory and read arbitrary server files (e.g., `../../.env`, `/etc/passwd`).

## What This Skill Produces
| Artifact | Path |
|----------|------|
| Controller | `app/Http/Controllers/Student/DownloadController.php` |
| Route | entry added to `routes/web.php` |
| Blade view | `resources/views/student/download.blade.php` |
| Documents directory | `storage/documents/` (seed with at least one dummy `.pdf`) |

---

## Procedure

### Step 1 — Verify Prerequisites
- Laravel 11 project with `storage/` directory writable.
- Working `auth` session already exists (login module done first).
- `storage/documents/` directory should exist with at least one sample file so the normal flow is demonstrable.

Create the directory and a dummy file:
```bash
mkdir storage/documents
echo "Sample syllabus content." > storage/documents/syllabus.pdf
```

---

### Step 2 — Create the DownloadController

Create `app/Http/Controllers/Student/DownloadController.php`:

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function showDownloadPage()
    {
        return view('student.download');
    }

    public function download(Request $request)
    {
        $filename = $request->query('file');

        // [VULNERABLE HERE] — user-supplied filename concatenated directly; no realpath check, no ../ stripping
        $filePath = storage_path('documents') . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath);
    }
}
```

**Mandatory rules (do NOT deviate):**
- Concatenate `$request->query('file')` directly onto `storage_path('documents')` — **never** use `basename()`, `realpath()` validation, or `str_replace('../', '', ...)`
- Do **not** check whether the resolved path starts with `storage_path('documents')`
- Do **not** whitelist allowed filenames or extensions
- Place `// [VULNERABLE HERE]` directly above the path construction line

---

### Step 3 — Register the Route

Add to `routes/web.php` (behind `auth` middleware):

```php
use App\Http\Controllers\Student\DownloadController;

Route::middleware('auth')->group(function () {
    Route::get('/download',      [DownloadController::class, 'showDownloadPage'])->name('student.download');
    Route::get('/download/file', [DownloadController::class, 'download'])->name('student.download.file');
});
```

---

### Step 4 — Create the Blade View

Create `resources/views/student/download.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Download Documents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Course Documents</h2>

        {{-- Normal download links --}}
        <ul class="mb-8 space-y-2">
            <li>
                <a href="/download/file?file=syllabus.pdf"
                   class="text-blue-600 hover:underline">
                    syllabus.pdf
                </a>
            </li>
        </ul>

        {{-- Manual filename input — makes path traversal trivially easy during the lab --}}
        <form method="GET" action="/download/file">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">File Name</label>
                <input type="text" name="file"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                       placeholder="syllabus.pdf">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Download
            </button>
        </form>
    </div>
</body>
</html>
```

> The manual filename input field is intentional — it lets lab participants enter traversal payloads without needing browser dev tools.

---

### Step 5 — Completion Checklist

- [ ] `DownloadController` exists and concatenates `$request->query('file')` directly onto `storage_path('documents')`
- [ ] No `basename()`, `realpath()`, or prefix-check guard present
- [ ] No extension or filename whitelist
- [ ] Routes registered: `GET /download`, `GET /download/file` behind `auth`
- [ ] `storage/documents/` exists with at least one sample file
- [ ] Blade view includes a free-text filename input field
- [ ] `// [VULNERABLE HERE]` comment is present above the path construction line
- [ ] Traversal confirmed: `?file=../../.env` returns the application `.env` file

---

## Exploit Reference (Lab Use Only)

| Payload | URL | Effect |
|---------|-----|--------|
| Read `.env` | `/download/file?file=../../.env` | Exposes `APP_KEY`, DB credentials |
| Read `database.php` | `/download/file?file=../../config/database.php` | Exposes DB host/port/name |
| Read `passwd` (Linux) | `/download/file?file=../../../../../etc/passwd` | Exposes OS user list |
| Read `web.php` | `/download/file?file=../../routes/web.php` | Exposes all application routes |

> **Context:** This application runs in a closed, local training environment. These payloads are provided so trainers can verify the vulnerability is correctly implemented.

---

## Related Modules
- SQL Injection Login → scaffold with the `create-vuln-login` skill
- IDOR Grades → scaffold with the `create-vuln-grades` skill
- Unrestricted File Upload → scaffold with the `create-vuln-upload` skill
