<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MoleculeRule extends Model
{
    protected $fillable = ['molecule_id', 'rule_file_path', 'version', 'is_active', 'notes'];

    public function molecule()
    {
        return $this->belongsTo(Molecule::class);
    }

    /**
     * JSON kuralları yükle ve parse et
     */
   public function getRules()
{
    // Storage yerine direkt file_exists kullanalım
    $fullPath = storage_path('app/' . $this->rule_file_path);

    Log::info("Attempting to read rule file", [
        'rule_file_path' => $this->rule_file_path,
        'full_path' => $fullPath,
        'exists' => file_exists($fullPath)
    ]);

    if (!file_exists($fullPath)) {
        Log::error("Rule file not found", ['path' => $fullPath]);
        return null;
    }

    try {
        $json = file_get_contents($fullPath);

        if ($json === false) {
            Log::error("Could not read file", ['path' => $fullPath]);
            return null;
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("JSON decode error", [
                'error' => json_last_error_msg(),
                'path' => $fullPath
            ]);
            return null;
        }

        Log::info("JSON decoded successfully", [
            'molecule_id' => $decoded['molecule_id'] ?? 'not set',
            'workflow_steps' => count($decoded['workflow'] ?? [])
        ]);

        return $decoded;

    } catch (\Exception $e) {
        Log::error("Exception reading rule file", [
            'path' => $fullPath,
            'error' => $e->getMessage()
        ]);
        return null;
    }
}

    /**
     * Kuralları güncelle
     */
    public function updateRules(array $rules)
    {
        $normalizedPath = str_replace('\\', '/', $this->rule_file_path);
        $json = json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put($normalizedPath, $json);

        $this->increment('version');
        return true;
    }
}
