<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function show()
    {
        // Vulnerable files in public/uploads
        $vulnerableFiles = collect(glob(public_path('uploads/*')))
            ->reject(fn($f) => basename($f) === '.gitkeep')
            ->map(fn($f) => [
                'name' => basename($f),
                'size' => round(filesize($f) / 1024, 2),
                'url'  => asset('uploads/' . basename($f)),
            ])->values();

        // Secure files in storage/app/private/avatars
        $secureFiles = collect([]);
        if (Storage::exists('private/avatars')) {
            $secureFiles = collect(Storage::files('private/avatars'))
                ->map(fn($f) => [
                    'name' => basename($f),
                    'size' => round(Storage::size($f) / 1024, 2),
                    'url'  => null, // Not directly accessible
                ])->values();
        }

        return view('student.upload', compact('vulnerableFiles', 'secureFiles'));
    }

    /**
     * LAYER 1: VULNERABLE UPLOAD (No validation, no secure naming, stored in public/)
     * [VULNERABLE HERE] — no MIME type check, no extension whitelist, no secure rename
     */
    public function uploadVulnerable(Request $request)
    {
        if (!$request->hasFile('avatar')) {
            return back()->with('error', 'No file selected.');
        }

        $file = $request->file('avatar');

        // File is stored directly in public/uploads/ — PHP files can be executed via web server
        $file->move(public_path('uploads'), $file->getClientOriginalName());

        $url = asset('uploads/' . $file->getClientOriginalName());

        return back()->with('success', "File uploaded: <a href=\"{$url}\" target=\"_blank\" class=\"underline text-blue-600\">{$file->getClientOriginalName()}</a>");
    }

    /**
     * LAYER 2: SECURE UPLOAD (With validation, secure naming, stored outside public/)
     */
    public function uploadSecure(Request $request)
    {
        // STEP 1: Strict Input Validation
        $request->validate([
            'avatar' => [
                'required',
                'image',              // Must be image format (jpeg, png, bmp, gif, svg, webp)
                'mimes:jpeg,png,jpg', // Only allow these 3 extensions
                'max:2048',           // Max 2MB (prevent DoS)
            ]
        ]);

        $file = $request->file('avatar');

        // STEP 2: Secure Random Naming
        // Example: 1742680923_aBcDeFgHiJ.jpg instead of hack.php
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // STEP 3: Store Outside Public Directory
        // Files stored in storage/app/private/avatars are NOT web-accessible
        $path = $file->storeAs('private/avatars', $fileName, 'local');

        return back()->with('success', "File safely stored in secure storage! Path: {$path}");
    }
}
