<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Student\UploadController;
use App\Http\Controllers\Student\DownloadController;


Route::get('/',    [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login']);
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/student/grades/{id}', [GradeController::class, 'show'])->name('student.grades.show');
    Route::get('/student/upload',  [UploadController::class, 'show'])->name('student.upload.show');
    Route::post('/student/upload', [UploadController::class, 'upload'])->name('student.upload.store');
    Route::get('/download',       [DownloadController::class, 'showDownloadPage'])->name('student.download');
    Route::get('/download/file',  [DownloadController::class, 'download'])->name('student.download.file');
});
