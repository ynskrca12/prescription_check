<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrescriptionService;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }//End

    public function prescription_index()
    {
        $branches = $this->prescriptionService->getAllBranches();
        return view('prescription.prescription_index', compact('branches'));
    }//End

    // AJAX: Branşa bağlı tanı kodları
    public function getDiagnosisCodes(int $branchId)
    {
        $diagnoses = $this->prescriptionService->getDiagnosisCodesByBranch($branchId);
        return response()->json($diagnoses);
    }

    // AJAX: Tanıya bağlı moleküller
    public function getMolecules(int $diagnosisId)
    {
        $molecules = $this->prescriptionService->getMoleculesByDiagnosis($diagnosisId);
        return response()->json($molecules);
    }

    // AJAX: Molekülün laboratuvar kuralları
    public function getLabRules(int $moleculeId)
    {
        $labRules = $this->prescriptionService->getLabRulesByMolecule($moleculeId);
        return response()->json($labRules);
    }

    // AJAX: Hasta lab değerlerine göre reçete uygunluğu
    public function checkEligibility(Request $request, int $moleculeId)
    {
        $patientLabResults = $request->input('lab_values', []);
        $result = $this->prescriptionService->checkPrescriptionEligibility($moleculeId, $patientLabResults);
        return response()->json($result);
    }
}
