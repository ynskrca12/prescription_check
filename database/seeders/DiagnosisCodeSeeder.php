<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\DiagnosisCode;

class DiagnosisCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [
            'Kardiyoloji' => [
                ['code' => 'I25.9', 'description' => 'Kronik İskemik Kalp Hastalığı'],
                ['code' => 'I50.9', 'description' => 'Kalp Yetmezliği'],
            ],
            'Endokrinoloji' => [
                ['code' => 'E11.9', 'description' => 'Tip 2 Diabetes Mellitus'],
                ['code' => 'E06.9', 'description' => 'Tiroidit'],
            ],
            'Nefroloji' => [
                ['code' => 'N18.9', 'description' => 'Kronik Böbrek Hastalığı'],
                ['code' => 'N04.9', 'description' => 'Nefrotik Sendrom'],
            ],
            'Hematoloji' => [
                ['code' => 'D50.9', 'description' => 'Demir Eksikliği Anemisi'],
                ['code' => 'D64.9', 'description' => 'Anemi'],
            ],
            'Onkoloji' => [
                ['code' => 'C78.9', 'description' => 'Malign Neoplazm'],
                ['code' => 'C80.1', 'description' => 'Malign Neoplazm'],
            ],
            'Romatoloji' => [
                ['code' => 'M79.9', 'description' => 'Yumuşak Doku Hastalığı'],
                ['code' => 'M25.9', 'description' => 'Eklem Hastalığı'],
            ],
        ];

        foreach ($data as $branchName => $diagnoses) {
            $branch = Branch::where('name', $branchName)->first();
            if (!$branch) continue;

            foreach ($diagnoses as $diag) {
                DiagnosisCode::create([
                    'branch_id'   => $branch->id,
                    'code'        => $diag['code'],
                    'description' => $diag['description'],
                ]);
            }
        }
    }
}
