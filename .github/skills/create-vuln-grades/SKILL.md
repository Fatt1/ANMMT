---
name: create-vuln-grades
description: 'Creates the intentionally vulnerable View Grades module (IDOR / OWASP A01) for the CTF security lab. Use when asked to scaffold the grades controller, grades route, or grades view. Produces: GradeController using Grade::find($id) with NO ownership check (no Auth::id() comparison, no Policies, no Gates), GET /student/grades/{id} route, and a Tailwind grades blade. Marks the vulnerable line with // [VULNERABLE HERE]. Do NOT use for production code.'
argument-hint: 'Optional extras, e.g. "show subject name" or "include download button"'
---

# Create Vulnerable View Grades Module (IDOR)

## When to Use
- "Create the grades module"
- "Scaffold the GradeController"
- "Build the view grades page for the lab"
- "Implement IDOR on the grades endpoint"
- Any request to generate the student grades / view-grades flow for the intentionally vulnerable student management app

## Vulnerability Target
**OWASP A01:2021 – Broken Access Control (Insecure Direct Object Reference)**
Any authenticated student can view another student's grades by changing the `{id}` in the URL (e.g., `/student/grades/1`, `/student/grades/2`, …). No ownership check is performed.

## What This Skill Produces
| Artifact | Path |
|----------|------|
| Model | `app/Models/Grade.php` |
| Migration | `database/migrations/xxxx_create_grades_table.php` |
| Controller | `app/Http/Controllers/Student/GradeController.php` |
| Route | entry added to `routes/web.php` |
| Blade view | `resources/views/student/grades.blade.php` |

---

## Procedure

### Step 1 — Verify Prerequisites
- Laravel 11 project with Eloquent available.
- A `users` table and working `Auth` session already exist (login module done first).
- `Grade` model and migration do **not** yet exist — this skill creates them.

---

### Step 2 — Create the Grade Migration

Create `database/migrations/xxxx_create_grades_table.php` (use `php artisan make:migration` or create manually):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->decimal('score', 5, 2);
            $table->string('semester')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
```

---

### Step 3 — Create the Grade Model

Create `app/Models/Grade.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['user_id', 'subject', 'score', 'semester'];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

---

### Step 4 — Create the GradeController

Create `app/Http/Controllers/Student/GradeController.php`:

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;

class GradeController extends Controller
{
    public function show(int $id)
    {
        // [VULNERABLE HERE] — no Auth::id() check; any authenticated user can view any grade record
        $grade = Grade::find($id);

        if (!$grade) {
            abort(404);
        }

        return view('student.grades', compact('grade'));
    }
}
```

**Mandatory rules (do NOT deviate):**
- Use `Grade::find($id)` — **never** `Grade::where('user_id', Auth::id())->findOrFail($id)`
- Do **not** compare `$grade->user_id` with `Auth::id()` anywhere
- Do **not** use `$this->authorize()`, Laravel Policies, or Gates
- Place `// [VULNERABLE HERE]` directly above the `Grade::find($id)` call

---

### Step 5 — Register the Route

Add to `routes/web.php` (inside an `auth` middleware group so students must be logged in — the vulnerability is that *any* logged-in student can access *any* ID):

```php
use App\Http\Controllers\Student\GradeController;

Route::middleware('auth')->group(function () {
    Route::get('/student/grades/{id}', [GradeController::class, 'show'])->name('student.grades.show');
});
```

---

### Step 6 — Create the Blade View

Create `resources/views/student/grades.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Grade Details</h2>

        <table class="w-full text-sm border-collapse">
            <tbody>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600 w-1/3">Record ID</td>
                    <td class="py-2">{{ $grade->id }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Student ID</td>
                    <td class="py-2">{{ $grade->user_id }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Subject</td>
                    <td class="py-2">{{ $grade->subject }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Score</td>
                    <td class="py-2">{{ $grade->score }}</td>
                </tr>
                <tr>
                    <td class="py-2 font-medium text-gray-600">Semester</td>
                    <td class="py-2">{{ $grade->semester ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="/student/grades/{{ $grade->id - 1 }}"
               class="text-blue-500 hover:underline mr-4">&larr; Previous</a>
            <a href="/student/grades/{{ $grade->id + 1 }}"
               class="text-blue-500 hover:underline">Next &rarr;</a>
        </div>
    </div>
</body>
</html>
```

> The Previous / Next navigation links are intentional — they make it trivially easy for students to walk through other users' records during the lab exercise.

---

### Step 7 — Completion Checklist

- [ ] `grades` migration exists with `user_id`, `subject`, `score`, `semester` columns
- [ ] `Grade` model exists at `app/Models/Grade.php`
- [ ] `GradeController::show()` uses `Grade::find($id)` with **no** ownership check
- [ ] No `Auth::id()` comparison, no `authorize()`, no Policy/Gate anywhere in the controller
- [ ] Route registered: `GET /student/grades/{id}` behind `auth` middleware
- [ ] Blade view displays `grade->user_id` (exposes whose record it is)
- [ ] `// [VULNERABLE HERE]` comment is present above `Grade::find($id)`
- [ ] IDOR confirmed: log in as student A, visit `/student/grades/<student_B_id>` → data is displayed

---

## Exploit Reference (Lab Use Only)

| Action | URL | Effect |
|--------|-----|--------|
| View own grade | `/student/grades/3` | Normal — your record |
| View another student's grade | `/student/grades/1` | IDOR — another student's data |
| Enumerate all records | `/student/grades/1`, `2`, `3`, … | Full data harvest by ID walking |

> **Context:** This application runs in a closed, local training environment. These payloads are provided so trainers can verify the vulnerability is correctly implemented.

---

## Related Modules
- SQL Injection Login → scaffold with the `create-vuln-login` skill
- Unrestricted File Upload → scaffold with the `create-vuln-upload` skill
- Path Traversal Download → scaffold with the `create-vuln-download` skill
