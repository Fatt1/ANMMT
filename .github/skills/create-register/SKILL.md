---
name: create-register
description: 'Creates a secure (non-vulnerable) user registration module for the CTF lab. Use when asked to scaffold the register controller, registration form, or sign-up page. Produces: RegisterController using Laravel validation + Eloquent User::create() + bcrypt hashing (no raw SQL, no missing validation), GET+POST /register routes, and a Tailwind register blade. This module is intentionally SECURE — use it as the "fixed" reference alongside the vulnerable modules.'
argument-hint: 'Optional extras, e.g. "add phone field" or "redirect to login instead of dashboard"'
---

# Create Secure Registration Module

## When to Use
- "Create the register / sign-up module"
- "Scaffold the RegisterController"
- "Build the registration form for the lab"
- "Add a secure registration page"
- Any request to create a user sign-up flow that is NOT vulnerable

## Security Properties (intentionally applied)
| Concern | Mitigation used |
|---------|-----------------|
| Mass assignment | Only `name`, `email`, `password` in `$fillable` |
| SQL Injection | Eloquent `User::create()` — no raw SQL |
| Weak passwords | `min:8` + `confirmed` rule |
| Password storage | Laravel `password` cast (`bcrypt` via `Hash::make()`) |
| Duplicate accounts | `unique:users,email` validation rule |
| CSRF | `@csrf` directive in the form |
| XSS output | `{{ }}` Blade escaping everywhere |

## What This Skill Produces
| Artifact | Path |
|----------|------|
| Controller | `app/Http/Controllers/RegisterController.php` |
| Routes | `GET /register`, `POST /register` added to `routes/web.php` |
| Blade view | `resources/views/auth/register.blade.php` |

---

## Procedure

### Step 1 — Verify Prerequisites
- `users` table migration exists and has `name`, `email`, `password` columns.
- `App\Models\User` has `password` in its `casts` array (set to `'hashed'`) **or** you call `Hash::make()` explicitly.
- No existing `RegisterController` at the target path.

---

### Step 2 — Create the RegisterController

Create `app/Http/Controllers/RegisterController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // hashed automatically by model cast
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
```

**Security rules (do NOT remove):**
- Always use `$request->validate()` — never skip validation
- Use Eloquent `User::create()` — never raw `DB::insert()`
- Pass only validated fields to `User::create()` — never `$request->all()`
- `password` is automatically hashed via the `'hashed'` cast on the User model

---

### Step 3 — Register Routes

Add to `routes/web.php` (as guest-only routes):

```php
use App\Http\Controllers\RegisterController;

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
```

---

### Step 4 — Create the Blade View

Create `resources/views/auth/register.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register — Student Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Create Account</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                <p class="text-xs text-gray-400 mt-1">Minimum 8 characters.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Register
            </button>
        </form>

        <p class="text-sm text-center text-gray-500 mt-4">
            Already have an account?
            <a href="/login" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
```

---

### Step 5 — Completion Checklist

- [ ] `RegisterController` uses `$request->validate()` with `name`, `email`, `password` + `confirmed` rules
- [ ] `unique:users,email` rule prevents duplicate accounts
- [ ] `User::create()` receives only validated fields — no `$request->all()`
- [ ] Password is hashed (via model cast or `Hash::make()`)
- [ ] Routes registered: `GET /register`, `POST /register`
- [ ] Blade form has `@csrf`, `password_confirmation` field, and `{{ old('name') }}` / `{{ old('email') }}`
- [ ] Errors displayed with `{{ }}` (XSS-safe) Blade escaping
- [ ] After registration: user is logged in and redirected to `/dashboard`

---

## Contrast with Vulnerable Modules
This module exists as the **secure reference** in the CTF lab. Students can compare it with the SQL Injection login module to understand what correct implementation looks like.

| Feature | Vulnerable Login | This Module |
|---------|-----------------|-------------|
| DB access | Raw `DB::select()` + concatenation | Eloquent `User::create()` |
| Validation | None | `$request->validate()` |
| Password | Plaintext comparison | `bcrypt` via model cast |
| SQL Injection | Possible | Not possible |

---

## Related Modules
- SQL Injection Login → scaffold with the `create-vuln-login` skill
- Dashboard landing page → scaffold with the `create-dashboard` skill
