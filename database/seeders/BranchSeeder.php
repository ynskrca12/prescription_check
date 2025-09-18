<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Branch::create([
            'name' => 'Hematoloji',
            'slug' => 'hema',
        ]);

        Branch::create([
            'name' => 'Kardiyoloji',
            'slug' => 'card',
        ]);

        Branch::create([
            'name' => 'Endokrinoloji',
            'slug' => 'endo',
        ]);

         Branch::create([
            'name' => 'Nefroloji',
            'slug' => 'neph',
        ]);

         Branch::create([
            'name' => 'Onkoloji',
            'slug' => 'onco',
        ]);

         Branch::create([
            'name' => 'Romatoloji',
            'slug' => 'rheu',
        ]);
    }
}
