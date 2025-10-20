<?php

use App\Http\Controllers\PrescriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prescription', [PrescriptionController::class, 'prescription_index'])->name('prescription.index');


// AJAX işlemleri için ayrı route grubu
Route::prefix('ajax/prescription')->group(function() {
    Route::get('/diagnosis/{branch}', [PrescriptionController::class, 'getDiagnosisCodes'])
        ->name('ajax.prescription.diagnosis');

    Route::get('/molecules/{diagnosis}', [PrescriptionController::class, 'getMolecules'])
        ->name('ajax.prescription.molecules');

    Route::get('/labrules/{molecule}', [PrescriptionController::class, 'getLabRules'])
        ->name('ajax.prescription.labrules');

    Route::post('/check/{molecule}', [PrescriptionController::class, 'checkEligibility'])
        ->name('ajax.prescription.check');



    Route::get('/diagnosis/{branchId}', [PrescriptionController::class, 'getDiagnosisCodes']);
    Route::get('/molecules/{diagnosisId}', [PrescriptionController::class, 'getMolecules']);
    Route::get('/workflow/{moleculeId}', [PrescriptionController::class, 'getMoleculeWorkflow']);
    Route::get('/parameter-details', [PrescriptionController::class, 'getParameterDetails']);
    Route::get('/labrules/{moleculeId}', [PrescriptionController::class, 'getLabRules']); // ✅ EKLE
    Route::post('/process-step/{moleculeId}', [PrescriptionController::class, 'processStep']);
});
