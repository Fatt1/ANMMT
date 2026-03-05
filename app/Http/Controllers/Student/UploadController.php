<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function show()
    {
        $files = collect(glob(public_path('uploads/*')))
            ->reject(fn($f) => basename($f) === '.gitkeep')
            ->map(fn($f) => [
                'name' => basename($f),
                'size' => round(filesize($f) / 1024, 2),
                'url'  => asset('uploads/' . basename($f)),
            ])->values();

        return view('student.upload', compact('files'));
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('avatar')) {
            return back()->with('error', 'No file selected.');
        }

        $file = $request->file('avatar');

        // [VULNERABLE HERE] — no MIME type check, no extension whitelist, no secure rename
        // File is stored directly in public/uploads/ — PHP files can be executed via web server
        $file->move(public_path('uploads'), $file->getClientOriginalName());

        $url = asset('uploads/' . $file->getClientOriginalName());

        return back()->with('success', "File uploaded: <a href=\"{$url}\" target=\"_blank\" class=\"underline text-blue-600\">{$file->getClientOriginalName()}</a>");
    }
}
