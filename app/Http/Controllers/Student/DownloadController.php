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

    /**
     * Fixed version: prevent path traversal by resolving and validating real path.
     */
    public function downloadSecure(Request $request)
    {
        $filename = $request->query('file');

        if (!$filename) {
            abort(400, 'Missing file parameter.');
        }

        $basePath = realpath(storage_path('documents'));
        if ($basePath === false) {
            abort(500, 'Documents directory is missing.');
        }

        $resolvedPath = realpath($basePath . DIRECTORY_SEPARATOR . $filename);

        // Only allow files that resolve inside storage/documents.
        if ($resolvedPath === false || !str_starts_with($resolvedPath, $basePath . DIRECTORY_SEPARATOR) || !is_file($resolvedPath)) {
            abort(403, 'Access denied.');
        }

        return response()->download($resolvedPath);
    }
}
