<?php

namespace App\Http\Controllers;

use App\Models\Grade;
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
            'password' => $validated['password'], // stored as plaintext (intentional for CTF lab)
        ]);

        $subjects  = ['Mathematics', 'Physics', 'Chemistry', 'Biology', 'History', 'Literature', 'English', 'Computer Science'];
        $semesters = ['2024-1', '2024-2', '2025-1', '2025-2'];

        $usedSubjects = [];
        for ($i = 0; $i < 5; $i++) {
            do {
                $subject = $subjects[array_rand($subjects)];
            } while (in_array($subject, $usedSubjects));
            $usedSubjects[] = $subject;

            Grade::create([
                'user_id'  => $user->id,
                'subject'  => $subject,
                'score'    => round(mt_rand(400, 1000) / 10, 1), // 40.0 – 100.0
                'semester' => $semesters[array_rand($semesters)],
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}

