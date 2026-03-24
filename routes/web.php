<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Student\UploadController;
use App\Http\Controllers\Student\DownloadController;
use App\Http\Controllers\Student\SqliLabController;


Route::get('/',    [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login']);
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/student/grades/{id}', [GradeController::class, 'show'])->name('student.grades.show');
    Route::get('/student/upload',  [UploadController::class, 'show'])->name('student.upload.show');
    Route::post('/student/upload/vulnerable', [UploadController::class, 'uploadVulnerable'])->name('student.upload.vulnerable');
    Route::post('/student/upload/secure', [UploadController::class, 'uploadSecure'])->name('student.upload.secure');
    Route::get('/download',       [DownloadController::class, 'showDownloadPage'])->name('student.download');
    Route::get('/download/file',  [DownloadController::class, 'download'])->name('student.download.file');
    Route::get('/sqli/blind',     [SqliLabController::class, 'showBlind'])->name('student.sqli.blind');
    Route::get('/sqli/blind/probe', [SqliLabController::class, 'probeBlind'])->name('student.sqli.blind.probe');
    Route::get('/sqli/union',     [SqliLabController::class, 'showUnion'])->name('student.sqli.union');
    Route::get('/product/detail', [SqliLabController::class, 'productDetailUnion'])->name('student.product.detail');
    Route::get('/sqli/oob',       [SqliLabController::class, 'showOob'])->name('student.sqli.oob');
    Route::get('/sqli/oob/test',  [SqliLabController::class, 'testOob'])->name('student.sqli.oob.test');
});
