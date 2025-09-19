<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DiagnosisCode;
use App\Models\Molecule;
use Illuminate\Support\Collection;

class PrescriptionService
{
    // Tüm branşları getir
    public function getAllBranches()
    {
        return Branch::orderBy('name')->get();
    }

    // Branşa bağlı tanı kodlarını getir
    public function getDiagnosisCodesByBranch(int $branchId)
    {
        return DiagnosisCode::where('branch_id', $branchId)
                            ->orderBy('code')
                            ->get(['id', 'code', 'description']); // Only select needed fields
    }

    // Tanıya bağlı molekülleri getir
    public function getMoleculesByDiagnosis(int $diagnosisId)
    {
        return DiagnosisCode::findOrFail($diagnosisId)
                            ->molecules()
                            ->orderBy('name')
                            ->get(['molecules.id', 'molecules.name', 'molecules.generic_name']);
    }

    // Molekül seçildiğinde laboratuvar kurallarını getir
    public function getLabRulesByMolecule(int $moleculeId): Collection
    {
        $molecule = Molecule::with(['labRules.parameter:id,name,unit'])
                           ->findOrFail($moleculeId);

        return $molecule->labRules->map(function($rule) {
            return [
                'id' => $rule->id,
                'laboratory_parameter_id' => $rule->laboratory_parameter_id,
                'operator' => $rule->operator,
                'value' => $rule->value,
                'parameter' => [
                    'name' => $rule->parameter->name,
                    'unit' => $rule->parameter->unit ?? ''
                ]
            ];
        });
    }

    // Hasta lab değerlerini kontrol et ve reçete yazılabilir mi döndür
    public function checkPrescriptionEligibility(int $moleculeId, array $patientLabResults)
    {
        $labRules = $this->getLabRulesByMolecule($moleculeId);
        $result = ['eligible' => true, 'messages' => []];

        foreach ($labRules as $rule) {
            $paramId = $rule['laboratory_parameter_id'];
            $paramName = $rule['parameter']['name'];
            $patientValue = $patientLabResults[$paramId] ?? null;

            if ($patientValue === null) {
                $result['eligible'] = false;
                $result['messages'][] = "$paramName değeri girilmemiş.";
                continue;
            }

            switch ($rule['operator']) {
                case '>=':
                    if ($patientValue < $rule['value']) $result['eligible'] = false;
                    break;
                case '<=':
                    if ($patientValue > $rule['value']) $result['eligible'] = false;
                    break;
                case '>':
                    if ($patientValue <= $rule['value']) $result['eligible'] = false;
                    break;
                case '<':
                    if ($patientValue >= $rule['value']) $result['eligible'] = false;
                    break;
                case '=':
                    if ($patientValue != $rule['value']) $result['eligible'] = false;
                    break;
            }

            if (!$result['eligible']) {
                $result['messages'][] = "$paramName için reçete kurallarına uymuyor (Gerekli: {$rule['operator']} {$rule['value']}, Girilen: $patientValue)";
            }
        }

        if ($result['eligible']) {
            $result['messages'][] = "Reçete yazılabilir. Laboratuvar değerleri kurallara uygun.";
        }

        return $result;
    }
}
