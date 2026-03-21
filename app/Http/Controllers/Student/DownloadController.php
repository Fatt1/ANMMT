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
