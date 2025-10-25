<?php

use App\Http\Controllers\PhysicianAuthController;
use App\Http\Controllers\PrescriptionController;
use Illuminate\Support\Facades\Route;

// Ana sayfa - login'e yönlendir
Route::get('/', function () {
    return redirect()->route('physician.login');
});

// Hekim Authentication Routes
Route::prefix('physician')->name('physician.')->group(function () {
    // Guest routes (giriş yapmamışlar için)
    Route::middleware('guest:physician')->group(function () {
        Route::get('/login', [PhysicianAuthController::class, 'showLogin'])->name('login');
        Route::get('/qr-codes', [PhysicianAuthController::class, 'showQrCode'])->name('qr-codes');
        Route::get('/password/{physicianCode}', [PhysicianAuthController::class, 'showPasswordForm'])->name('password');
        Route::post('/login', [PhysicianAuthController::class, 'login'])->name('login.post');
    });

    // Auth routes (giriş yapmışlar için)
    // Route::middleware('physician.auth')->group(function () {
        Route::post('/logout', [PhysicianAuthController::class, 'logout'])->name('logout');
    // });
});

// Prescription Routes (Hekim girişi gerekli)
// Route::middleware('physician.auth')->group(function () {
    Route::get('/prescription', [PrescriptionController::class, 'prescription_index'])->name('prescription.index');

    Route::prefix('ajax/prescription')->group(function () {
        Route::get('/diagnosis/{branchId}', [PrescriptionController::class, 'getDiagnosisCodes']);
        Route::get('/molecules/{diagnosisId}', [PrescriptionController::class, 'getMolecules']);
        Route::get('/workflow/{moleculeId}', [PrescriptionController::class, 'getMoleculeWorkflow']);
        Route::post('/process-step/{moleculeId}', [PrescriptionController::class, 'processStep']);
    });
// });
