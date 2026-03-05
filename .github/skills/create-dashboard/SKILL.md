---
name: create-dashboard
description: 'Creates the CTF lab Dashboard — a central landing page that links to all four vulnerable modules (SQL Injection login, IDOR grades, Unrestricted File Upload, Path Traversal download). Use when asked to scaffold the dashboard, build the student home page, or create a lab index. Produces: DashboardController, GET /dashboard route behind auth middleware, and a Tailwind blade with module cards. Requires all four vuln modules to be scaffolded first.'
argument-hint: 'Optional extras, e.g. "show current user name" or "add module descriptions"'
---

# Create CTF Lab Dashboard (Landing Page)

## When to Use
- "Create the dashboard"
- "Build the student home page"
- "Scaffold the lab index / landing page"
- "Add a central starting point for the CTF lab"
- Any request to create a hub that links to all four vulnerable modules

## What This Skill Produces
| Artifact | Path |
|----------|------|
| Controller | `app/Http/Controllers/DashboardController.php` |
| Route | `GET /dashboard` added to `routes/web.php` |
| Blade view | `resources/views/dashboard.blade.php` |

## Prerequisites
All four vulnerable modules must be scaffolded first (routes must exist):
| Module | Route | Skill |
|--------|-------|-------|
| SQL Injection Login | `GET /login` | `create-vuln-login` |
| IDOR Grades | `GET /student/grades/{id}` | `create-vuln-grades` |
| Unrestricted File Upload | `GET /student/upload` | `create-vuln-upload` |
| Path Traversal Download | `GET /download` | `create-vuln-download` |

---

## Procedure

### Step 1 — Create the DashboardController

Create `app/Http/Controllers/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', ['user' => Auth::user()]);
    }
}
```

---

### Step 2 — Register the Route

Add to `routes/web.php` inside (or alongside) the `auth` middleware group:

```php
use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

---

### Step 3 — Create the Blade View

Create `resources/views/dashboard.blade.php` with four module cards, each linking to the respective vulnerable endpoint:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CTF Lab — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between">
        <span class="font-bold text-lg tracking-wide">🎓 Student Management — Security Lab</span>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-300">{{ $user->name ?? $user->email }}</span>
            <form method="POST" action="/logout">
                @csrf
                <button class="text-sm bg-red-600 hover:bg-red-700 px-3 py-1 rounded">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Lab Dashboard</h1>
        <p class="text-gray-500 mb-10">Select a module below to begin the exercise.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Module 1: SQL Injection --}}
            <a href="/login"
               class="block bg-white rounded-xl shadow hover:shadow-md transition p-6 border-l-4 border-red-500">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">💉</span>
                    <h2 class="text-xl font-semibold text-gray-800">SQL Injection</h2>
                </div>
                <p class="text-gray-500 text-sm mb-3">Bypass authentication without a valid password using SQL injection in the email field.</p>
                <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-1 rounded">OWASP A03 · Injection</span>
            </a>

            {{-- Module 2: IDOR --}}
            <a href="/student/grades/1"
               class="block bg-white rounded-xl shadow hover:shadow-md transition p-6 border-l-4 border-yellow-500">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">🔓</span>
                    <h2 class="text-xl font-semibold text-gray-800">IDOR — View Grades</h2>
                </div>
                <p class="text-gray-500 text-sm mb-3">Access other students' grade records by manipulating the ID in the URL.</p>
                <span class="text-xs font-medium bg-yellow-100 text-yellow-700 px-2 py-1 rounded">OWASP A01 · Broken Access Control</span>
            </a>

            {{-- Module 3: Unrestricted File Upload --}}
            <a href="/student/upload"
               class="block bg-white rounded-xl shadow hover:shadow-md transition p-6 border-l-4 border-orange-500">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">📤</span>
                    <h2 class="text-xl font-semibold text-gray-800">Unrestricted File Upload</h2>
                </div>
                <p class="text-gray-500 text-sm mb-3">Upload a PHP web shell and execute arbitrary commands on the server.</p>
                <span class="text-xs font-medium bg-orange-100 text-orange-700 px-2 py-1 rounded">OWASP A04 · Insecure Design</span>
            </a>

            {{-- Module 4: Path Traversal --}}
            <a href="/download"
               class="block bg-white rounded-xl shadow hover:shadow-md transition p-6 border-l-4 border-purple-500">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">📂</span>
                    <h2 class="text-xl font-semibold text-gray-800">Path Traversal — Download</h2>
                </div>
                <p class="text-gray-500 text-sm mb-3">Read arbitrary server files such as <code class="text-xs bg-gray-100 px-1 rounded">.env</code> by traversing directories with <code class="text-xs bg-gray-100 px-1 rounded">../</code>.</p>
                <span class="text-xs font-medium bg-purple-100 text-purple-700 px-2 py-1 rounded">OWASP A01 · Broken Access Control</span>
            </a>

        </div>
    </main>

</body>
</html>
```

---

### Step 4 — Update Login Redirect

Ensure the login module redirects to `/dashboard` on success (not `/home`). In `LoginController::login()`:

```php
return redirect('/dashboard');
```

---

### Step 5 — Completion Checklist

- [ ] `DashboardController` exists at `app/Http/Controllers/DashboardController.php`
- [ ] `GET /dashboard` route registered behind `auth` middleware
- [ ] Blade view has four module cards with correct links
- [ ] Each card shows the OWASP category label
- [ ] Logout button posts to `POST /logout`
- [ ] Login redirect points to `/dashboard`
- [ ] Visiting `/dashboard` while unauthenticated redirects to `/login`

---

## Related Modules
- SQL Injection Login → scaffold with the `create-vuln-login` skill
- IDOR Grades → scaffold with the `create-vuln-grades` skill
- Unrestricted File Upload → scaffold with the `create-vuln-upload` skill
- Path Traversal Download → scaffold with the `create-vuln-download` skill
