<?php

namespace App\Http\Controllers;

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
