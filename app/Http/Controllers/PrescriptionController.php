<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\DiagnosisCode;
use App\Models\Molecule;
use App\Models\MoleculeRule;
use App\Models\LaboratoryParameter;
use App\Models\MoleculeLabRule;
use App\Services\PrescriptionService;

class PrescriptionController extends Controller
{
    // protected $prescriptionService;

    // public function __construct(PrescriptionService $prescriptionService)
    // {
    //     $this->prescriptionService = $prescriptionService;
    // }//End

    // public function prescription_index()
    // {
    //     $branches = $this->prescriptionService->getAllBranches();
    //     return view('prescription.prescription_index', compact('branches'));
    // }//End

    // // AJAX: Branşa bağlı tanı kodları
    // public function getDiagnosisCodes(int $branchId)
    // {
    //     $diagnoses = $this->prescriptionService->getDiagnosisCodesByBranch($branchId);
    //     return response()->json($diagnoses);
    // }

    // // AJAX: Tanıya bağlı moleküller
    // public function getMolecules(int $diagnosisId)
    // {
    //     $molecules = $this->prescriptionService->getMoleculesByDiagnosis($diagnosisId);
    //     return response()->json($molecules);
    // }

    // // AJAX: Molekülün laboratuvar kuralları
    // public function getLabRules(int $moleculeId)
    // {
    //     $labRules = $this->prescriptionService->getLabRulesByMolecule($moleculeId);
    //     return response()->json($labRules);
    // }

    // // AJAX: Hasta lab değerlerine göre reçete uygunluğu
    // public function checkEligibility(Request $request, int $moleculeId)
    // {
    //     $patientLabResults = $request->input('lab_values', []);
    //     $result = $this->prescriptionService->checkPrescriptionEligibility($moleculeId, $patientLabResults);
    //     return response()->json($result);
    // }

     public function prescription_index()
    {
        $branches = Branch::orderBy('name')->get();
        return view('prescription.prescription_index', compact('branches'));
    }

    /**
     * Branşa göre tanı kodları
     */
    public function getDiagnosisCodes($branchId)
    {
        $diagnoses = DiagnosisCode::where('branch_id', $branchId)
            ->orderBy('code')
            ->get();

        return response()->json($diagnoses);
    }

    /**
     * Tanıya göre moleküller
     */
    public function getMolecules($diagnosisId)
    {
        $molecules = Molecule::whereHas('diagnoses', function($q) use ($diagnosisId) {
            $q->where('diagnosis_code_id', $diagnosisId);
        })->orderBy('name')->get();

        return response()->json($molecules);
    }

    /**
     * Molekül için lab kurallarını getir (eski sistem için)
     */
    public function getLabRules($moleculeId)
    {
        $rules = MoleculeLabRule::where('molecule_id', $moleculeId)
            ->with('parameter')
            ->get();

        return response()->json($rules);
    }

    /**
     * Molekül için kural workflow'unu yükle
     */
    public function getMoleculeWorkflow($moleculeId)
    {
        $moleculeRule = MoleculeRule::where('molecule_id', $moleculeId)
            ->where('is_active', true)
            ->first();

        if (!$moleculeRule) {
            return response()->json([
                'has_rules' => false,
                'message' => 'Bu molekül için henüz kural tanımlanmamış.'
            ]);
        }

        $rules = $moleculeRule->getRules();

        if (!$rules) {
            return response()->json([
                'has_rules' => false,
                'message' => 'Kural dosyası okunamadı.'
            ]);
        }

        return response()->json([
            'has_rules' => true,
            'workflow' => $rules['workflow'],
            'molecule_info' => [
                'id' => $rules['molecule_id'],
                'name' => $rules['molecule_name'],
                'version' => $rules['version']
            ]
        ]);
    }

    /**
     * Lab parametrelerini detaylı bilgilerle getir
     */
    public function getParameterDetails(Request $request)
    {
        $parameterIds = $request->parameter_ids;

        $parameters = LaboratoryParameter::whereIn('id', $parameterIds)->get();

        return response()->json($parameters);
    }

    /**
     * Workflow adımını işle ve sonucu döndür
     */
    public function processStep(Request $request, $moleculeId)
    {
        $moleculeRule = MoleculeRule::where('molecule_id', $moleculeId)
            ->where('is_active', true)
            ->first();

        if (!$moleculeRule) {
            return response()->json([
                'success' => false,
                'message' => 'Kural bulunamadı'
            ], 404);
        }

        $rules = $moleculeRule->getRules();
        $stepId = $request->step_id;
        $userAnswers = $request->answers ?? [];
        $labValues = $request->lab_values ?? [];
        $storedVariables = $request->stored_variables ?? [];

        // İlgili adımı bul
        $step = collect($rules['workflow'])->firstWhere('id', $stepId);

        if (!$step) {
            return response()->json([
                'success' => false,
                'message' => 'Adım bulunamadı'
            ], 404);
        }

        $result = $this->evaluateStep($step, $userAnswers, $labValues, $storedVariables);

        // Sonucu logla (opsiyonel)
        if ($result['is_final']) {
            \App\Models\PrescriptionCheck::create([
                'user_id' => 1, // auth()->id() olacak ilerde
                'molecule_id' => $moleculeId,
                'answers' => $userAnswers,
                'lab_values' => $labValues,
                'is_eligible' => $result['eligible'],
                'result_message' => $result['message']
            ]);
        }

        return response()->json($result);
    }

    /**
     * Adım değerlendirme motoru
     */
    private function evaluateStep($step, $userAnswers, $labValues, $storedVariables)
    {
        switch ($step['type']) {
            case 'prerequisite_question':
                return $this->evaluatePrerequisiteQuestion($step, $userAnswers);

            case 'info_message':
                return [
                    'success' => true,
                    'blocked' => false,
                    'is_final' => false,
                    'next_step' => $step['next_step']
                ];

            case 'lab_parameters_input':  // ✅ BU CASE'İ EKLE
                // Sadece input toplama adımı, backend'de değerlendirme yok
                return [
                    'success' => true,
                    'blocked' => false,
                    'is_final' => false,
                    'needs_input' => true,
                    'parameters' => $step['parameters'],
                    'next_step' => $step['next_step']
                ];

            case 'adinamik_question':  // ✅ BU CASE'İ EKLE
                return $this->evaluatePrerequisiteQuestion($step, $userAnswers);

            case 'complex_criteria_check':
                return $this->evaluateComplexCriteria($step, $labValues, $storedVariables);

            case 'lab_parameters':
                return $this->evaluateLabParameters($step, $labValues);

            case 'conditional_lab_check':
                return $this->evaluateConditionalLabCheck($step, $labValues, $storedVariables);

            case 'termination_warning':
            case 'blocking_message':
                return [
                    'success' => true,
                    'blocked' => false,
                    'is_final' => true,
                    'eligible' => true,
                    'message' => $step['message'] ?? 'Bilgilendirme tamamlandı',
                    'next_step' => 'end'
                ];

            default:
                return [
                    'success' => false,
                    'blocked' => false,
                    'is_final' => false,
                    'message' => 'Bilinmeyen adım tipi: ' . $step['type']
                ];
        }
    }

    private function evaluatePrerequisiteQuestion($step, $userAnswers)
    {
        $answer = $userAnswers[$step['id']] ?? null;

        if ($answer === null && ($step['required'] ?? false)) {
            return [
                'success' => false,
                'message' => 'Bu soru cevaplanmalıdır.'
            ];
        }

        // Blocking rules kontrolü
        if (isset($step['blocking_rules'])) {
            foreach ($step['blocking_rules'] as $rule) {
                if ($rule['answer'] === $answer && $rule['action'] === 'block') {
                    return [
                        'success' => true,
                        'blocked' => true,
                        'is_final' => true,
                        'eligible' => false,
                        'message' => $rule['message'],
                        'next_step' => 'end'
                    ];
                }
            }
        }

        // Bir sonraki adımı belirle
        $nextStep = null;
        if (isset($step['next_step'])) {
            if (is_array($step['next_step'])) {
                $nextStep = $step['next_step'][$answer] ?? 'end';
            } else {
                $nextStep = $step['next_step'];
            }
        }

        // Store variable if needed
        $storeAs = $step['store_as'] ?? null;

        return [
            'success' => true,
            'blocked' => false,
            'is_final' => false,  // ✅ BURAYI KONTROL ET
            'next_step' => $nextStep,
            'store_variable' => $storeAs ? [$storeAs => $answer] : null
        ];
    }

    private function evaluateLabParameters($step, $labValues)
    {
        $parameters = $step['parameters'];
        $logic = $step['logic'] ?? 'AND';
        $results = [];

        foreach ($parameters as $param) {
            $paramName = $param['name'];
            $userValue = $labValues[$paramName] ?? null;

            if ($userValue === null) {
                return [
                    'success' => false,
                    'message' => "{$param['label']} değeri girilmelidir."
                ];
            }

            // Eğer operator ve value varsa kontrol et, yoksa sadece değeri kaydet
            if (isset($param['operator']) && isset($param['value'])) {
                $expectedValue = $param['value'];
                $operator = $param['operator'];

                $passed = $this->compareValues($userValue, $operator, $expectedValue);

                $results[] = [
                    'parameter' => $paramName,
                    'passed' => $passed,
                    'user_value' => $userValue,
                    'expected' => $operator . ' ' . $expectedValue
                ];
            }
        }

        // Eğer hiç kontrol yoksa (sadece input toplama adımı)
        if (empty($results)) {
            return [
                'success' => true,
                'blocked' => false,
                'is_final' => false,  // ✅ BURAYI EKLE
                'next_step' => $step['next_step'] ?? 'end'
            ];
        }

        $allPassed = collect($results)->every(fn($r) => $r['passed']);
        $anyPassed = collect($results)->some(fn($r) => $r['passed']);

        $eligible = ($logic === 'AND') ? $allPassed : $anyPassed;

        return [
            'success' => true,
            'blocked' => false,
            'is_final' => true,  // ✅ BURAYI EKLE
            'eligible' => $eligible,
            'message' => $eligible ? ($step['success_message'] ?? 'Başarılı') : ($step['failure_message'] ?? 'Başarısız'),
            'results' => $results,
            'next_step' => $eligible ? ($step['next_step'] ?? 'end') : 'end'
        ];
    }

    private function evaluateConditionalLabCheck($step, $labValues, $storedVariables)
    {
        $conditions = $step['conditions'];

        foreach ($conditions as $condition) {
            $ifCondition = $condition['if'];

            // Koşulu kontrol et
            if ($this->checkCondition($ifCondition, $storedVariables, $labValues)) {
                $thenAction = $condition['then'];

                // Dynamic calculation varsa işle
                if ($thenAction['type'] === 'lab_parameters') {
                    foreach ($thenAction['parameters'] as &$param) {
                        if (isset($param['operator']) && $param['operator'] === 'dynamic') {
                            // Hesaplamayı yap
                            $calculation = $param['calculation'];
                            $param['value'] = $this->evaluateCalculation($calculation, $storedVariables, $labValues);
                            $param['operator'] = $param['operator_type'];
                        }
                    }
                    return $this->evaluateLabParameters($thenAction, $labValues);
                }

                if ($thenAction['type'] === 'blocking_message') {
                    return [
                        'success' => true,
                        'blocked' => true,
                        'is_final' => true,
                        'eligible' => false,
                        'message' => $thenAction['message'],
                        'next_step' => 'end'
                    ];
                }
            }
        }

        return [
            'success' => false,
            'message' => 'Hiçbir koşul eşleşmedi.'
        ];
    }

    private function checkCondition($condition, $storedVariables, $labValues)
    {
        $variable = $condition['variable'];
        $value = $storedVariables[$variable] ?? $labValues[$variable] ?? null;

        // Exists kontrolü
        if (isset($condition['exists'])) {
            $result = ($value !== null) === $condition['exists'];

            // AND koşulu varsa
            if (isset($condition['and']) && $result) {
                return $this->checkCondition($condition['and'], $storedVariables, $labValues);
            }

            return $result;
        }

        // Operator kontrolü
        if (isset($condition['operator'])) {
            $result = $this->compareValues($value, $condition['operator'], $condition['value']);

            if (isset($condition['and']) && $result) {
                return $this->checkCondition($condition['and'], $storedVariables, $labValues);
            }

            return $result;
        }

        return false;
    }

    private function compareValues($actual, $operator, $expected)
    {
        switch ($operator) {
            case '>=': return $actual >= $expected;
            case '<=': return $actual <= $expected;
            case '>': return $actual > $expected;
            case '<': return $actual < $expected;
            case '=': return $actual == $expected;
            default: return false;
        }
    }

    private function evaluateCalculation($expression, $storedVariables, $labValues)
    {
        // Basit matematiksel ifadeleri değerlendir
        // Örnek: "previous_ige * 0.8"

        foreach ($storedVariables as $key => $value) {
            $expression = str_replace($key, $value, $expression);
        }

        foreach ($labValues as $key => $value) {
            $expression = str_replace($key, $value, $expression);
        }

        // eval() güvenli değil, bu yüzden basit bir parser kullanabiliriz
        // Şimdilik eval kullanalım ama production'da bir math parser library kullanın
        try {
            return eval("return {$expression};");
        } catch (\Exception $e) {
            return 0;
        }
    }


    private function evaluateComplexCriteria($step, $labValues, $storedVariables)
{
    $criteria = $step['criteria'];
    $logic = $step['logic'] ?? 'OR'; // Default OR
    $metCriteria = [];
    $allResults = [];

    foreach ($criteria as $criterion) {
        $result = $this->evaluateSingleCriterion($criterion, $labValues, $storedVariables);

        $allResults[] = [
            'criterion_id' => $criterion['criterion_id'],
            'name' => $criterion['name'],
            'met' => $result['met'],
            'message' => $result['message']
        ];

        if ($result['met']) {
            $metCriteria[] = $criterion['name'] . ': ' . $result['message'];
        }
    }

    // Logic'e göre değerlendirme
    $anyMet = collect($allResults)->some(fn($r) => $r['met']);
    $allMet = collect($allResults)->every(fn($r) => $r['met']);

    $eligible = ($logic === 'AND') ? $allMet : $anyMet;

    // Mesajları oluştur
    $metCriteriaText = empty($metCriteria)
        ? 'Hiçbir kriter karşılanmadı'
        : implode("\n", array_map(fn($c, $i) => ($i+1) . ". " . $c, $metCriteria, array_keys($metCriteria)));

    $allCriteriaText = implode("\n", array_map(function($r, $i) {
        $status = $r['met'] ? '✅' : '❌';
        return "{$status} " . ($i+1) . ". {$r['name']}\n   → {$r['message']}";
    }, $allResults, array_keys($allResults)));

    $message = $eligible
        ? str_replace('{met_criteria}', $metCriteriaText, $step['overall_success_message'])
        : str_replace('{all_criteria_results}', $allCriteriaText, $step['overall_failure_message']);

    return [
        'success' => true,
        'blocked' => false,
        'is_final' => false,
        'eligible' => $eligible,
        'message' => $message,
        'criteria_results' => $allResults,
        'met_criteria_count' => count($metCriteria),
        'next_step' => $step['next_step']
    ];
}

private function evaluateSingleCriterion($criterion, $labValues, $storedVariables)
{
    switch ($criterion['type']) {
        case 'calculated_check':
            return $this->evaluateCalculatedCheck($criterion, $labValues, $storedVariables);

        case 'combined_check':
            return $this->evaluateCombinedCheck($criterion, $labValues, $storedVariables);

        case 'nested_check':
            return $this->evaluateNestedCheck($criterion, $labValues, $storedVariables);

        default:
            return ['met' => false, 'message' => 'Bilinmeyen kriter tipi'];
    }
}

private function evaluateCalculatedCheck($criterion, $labValues, $storedVariables)
{
    $calculation = $criterion['calculation'];
    $formula = $calculation['formula'];
    $variables = $calculation['variables'];
    $resultName = $calculation['result_name'];

    // Değişkenleri formülde değiştir
    $expression = $formula;
    foreach ($variables as $var) {
        $value = $labValues[$var] ?? $storedVariables[$var] ?? 0;
        $expression = str_replace($var, $value, $expression);
    }

    // Hesapla
    try {
        $calculatedValue = eval("return {$expression};");
    } catch (\Exception $e) {
        return ['met' => false, 'message' => 'Hesaplama hatası'];
    }

    // Koşulu kontrol et
    $condition = $criterion['condition'];
    $met = $this->compareValues(
        $calculatedValue,
        $condition['operator'],
        $condition['value']
    );

    // Mesajı oluştur - SAFE ACCESS ✅
    $messageTemplate = $met
        ? ($criterion['success_message'] ?? 'Kriter karşılandı')
        : ($criterion['failure_message'] ?? 'Kriter karşılanmadı');

    $message = str_replace(
        '{' . $resultName . '}',
        number_format($calculatedValue, 2),
        $messageTemplate
    );

    // Lab değerlerini de yerine koy
    foreach ($labValues as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }

    // Stored variables'ı da yerine koy
    foreach ($storedVariables as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }

    return [
        'met' => $met,
        'message' => $message,
        'calculated_value' => $calculatedValue
    ];
}

private function evaluateCombinedCheck($criterion, $labValues, $storedVariables)
{
    $conditions = $criterion['conditions'];
    $logic = $conditions['logic'] ?? 'AND';
    $rules = $conditions['rules'];
    $results = [];

    foreach ($rules as $rule) {
        $variable = $rule['variable'];
        $value = $labValues[$variable] ?? $storedVariables[$variable] ?? null;

        if ($value === null) {
            $results[] = false;
            continue;
        }

        $passed = $this->compareValues($value, $rule['operator'], $rule['value']);
        $results[] = $passed;
    }

    $met = ($logic === 'AND')
        ? !in_array(false, $results)
        : in_array(true, $results);

    // SAFE ACCESS ✅
    $messageTemplate = $met
        ? ($criterion['success_message'] ?? 'Kriter karşılandı')
        : ($criterion['failure_message'] ?? 'Kriter karşılanmadı');

    // Değişkenleri yerine koy
    $message = $messageTemplate;
    foreach ($labValues as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }
    foreach ($storedVariables as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }

    return [
        'met' => $met,
        'message' => $message
    ];
}

private function evaluateNestedCheck($criterion, $labValues, $storedVariables)
{
    $conditions = $criterion['conditions'];
    $logic = $conditions['logic'] ?? 'AND';
    $rules = $conditions['rules'];
    $results = [];

    foreach ($rules as $rule) {
        if (isset($rule['type']) && $rule['type'] === 'calculated_check') {
            // İçiçe calculated check
            $nestedResult = $this->evaluateCalculatedCheck($rule, $labValues, $storedVariables);
            $results[] = $nestedResult['met'];
        } else {
            // Normal rule
            $variable = $rule['variable'];
            $value = $labValues[$variable] ?? $storedVariables[$variable] ?? null;

            if ($value === null) {
                $results[] = false;
                continue;
            }

            $passed = $this->compareValues($value, $rule['operator'], $rule['value']);
            $results[] = $passed;
        }
    }

    $met = ($logic === 'AND')
        ? !in_array(false, $results)
        : in_array(true, $results);

    // SAFE ACCESS ✅
    $messageTemplate = $met
        ? ($criterion['success_message'] ?? 'Kriter karşılandı')
        : ($criterion['failure_message'] ?? 'Kriter karşılanmadı');

    // Değişkenleri yerine koy
    $message = $messageTemplate;
    foreach ($labValues as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }
    foreach ($storedVariables as $key => $value) {
        $message = str_replace('{' . $key . '}', $value, $message);
    }

    return [
        'met' => $met,
        'message' => $message
    ];
}
}
