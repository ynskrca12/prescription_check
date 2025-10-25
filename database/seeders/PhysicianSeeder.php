<?php

namespace Database\Seeders;

use App\Models\Physician;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PhysicianSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->warn('Önce branch seeder çalıştırın!');
            return;
        }

        $names = ['Ahmet', 'Mehmet', 'Ayşe', 'Fatma', 'Ali', 'Zeynep', 'Can', 'Elif', 'Mustafa', 'Selin'];
        $surnames = ['Yılmaz', 'Kaya', 'Demir', 'Şahin', 'Çelik', 'Yıldız', 'Aydın', 'Özdemir', 'Arslan', 'Doğan'];

        for ($i = 1; $i <= 100; $i++) {
            Physician::create([
                'physician_code' => 'HKM' . str_pad($i, 4, '0', STR_PAD_LEFT), // HKM0001, HKM0002, ...
                'name' => $names[array_rand($names)],
                'surname' => $surnames[array_rand($surnames)],
                'tc_no' => '1' . str_pad($i, 10, '0', STR_PAD_LEFT), // 10000000001, ...
                'diploma_no' => 'DIP' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'branch_id' => $branches->random()->id,
                'password' => Hash::make('12345678'), // Varsayılan şifre
                'is_active' => true,
            ]);
        }

        $this->command->info('100 hekim başarıyla oluşturuldu!');
        $this->command->info('Varsayılan şifre: 12345678');
    }
}
