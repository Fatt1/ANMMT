---
name: create-vuln-login
description: 'Creates the intentionally vulnerable Login module (SQL Injection / OWASP A03) for the CTF security lab. Use when asked to scaffold the login controller, login route, or login view. Produces: LoginController using raw DB::select() with direct string concatenation (no prepared statements), GET+POST /login routes, and a Tailwind login blade. Marks the vulnerable line with // [VULNERABLE HERE]. Do NOT use for production code.'
argument-hint: 'Optional extras, e.g. "add remember-me field" or "include session flash message"'
---

# Create Vulnerable Login Module (SQL Injection)

## When to Use
- "Create the login module"
- "Scaffold the LoginController"
- "Build the authentication page for the lab"
- "Implement login with SQL Injection"
- Any request to generate the login / authentication flow for the intentionally vulnerable student management app

## Vulnerability Target
**OWASP A03:2021 – Injection (SQL Injection)**
Allows full authentication bypass by injecting SQL into the email field (e.g., `' OR '1'='1' --`). No valid password is required.

## What This Skill Produces
| Artifact | Path |
|----------|------|
| Controller | `app/Http/Controllers/LoginController.php` |
| Routes | entries added to `routes/web.php` |
| Blade view | `resources/views/auth/login.blade.php` |

---

## Procedure

### Step 1 — Verify Prerequisites
- Laravel 11 project with `DB` facade available.
- `users` table migration exists (`database/migrations/0001_01_01_000000_create_users_table.php`).
- `Auth::loginUsingId()` is acceptable (Eloquent `User::where()` must NOT be used).

---

### Step 2 — Create the LoginController

Create `app/Http/Controllers/Auth/LoginController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email    = $request->email;
        $password = $request->password;

        // [VULNERABLE HERE] — raw SQL with direct string concatenation; no bindings, no escaping
        $sql   = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $users = DB::select($sql);

        if (!empty($users)) {
            $user = $users[0];
            Auth::loginUsingId($user->id);
            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
```

**Mandatory rules (do NOT deviate):**
- Use `DB::select()` with the raw `$sql` string — **never** `User::where()`
- Concatenate `$request->email` and `$request->password` directly — **no** `?` placeholders or bindings
- Do **not** hash-compare passwords (`Hash::check()` is forbidden here)
- Place `// [VULNERABLE HERE]` directly above the `DB::select($sql)` call

---

### Step 3 — Register Routes

Add to `routes/web.php`:

```php
use App\Http\Controllers\Auth\LoginController;

Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
```

---

### Step 4 — Create the Blade View

Create `resources/views/auth/login.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="text" name="email"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                       placeholder="student@lab.local">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Login
            </button>
        </form>
    </div>
</body>
</html>
```

---

### Step 5 — Completion Checklist

- [ ] `LoginController` exists and uses `DB::select()` with raw string concatenation
- [ ] No prepared-statement bindings (`?` placeholders) anywhere in the query
- [ ] `Auth::loginUsingId()` used — not `Auth::attempt()`
- [ ] Routes registered: `GET /login`, `POST /login`, `POST /logout`
- [ ] Blade view renders a form with `email` + `password` fields
- [ ] `// [VULNERABLE HERE]` comment is present above the vulnerable line
- [ ] Bypass confirmed: entering `' OR '1'='1' --` as email (any password) grants a session

---

## Exploit Reference (Lab Use Only)

| Email Input | Password | Effect |
|-------------|----------|--------|
| `' OR '1'='1' --` | anything | Logs in as the first user in the table |
| `admin@lab.local' --` | anything | Logs in as that specific user, skipping password |
| `' UNION SELECT 1,2,3,4,5 --` | anything | Probes for column count / data exfiltration |

> **Context:** This application runs in a closed, local training environment. These payloads are provided so trainers can verify the vulnerability is correctly implemented.

---

## Related Modules
- IDOR Grades → scaffold with the `create-vuln-grades` skill
- Unrestricted File Upload → scaffold with the `create-vuln-upload` skill
- Path Traversal Download → scaffold with the `create-vuln-download` skill
