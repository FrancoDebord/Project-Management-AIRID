<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProLabTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

         $all_lab_tests= [
            ['name' => 'Cone Test', "level_type" => 1],
            ['name' => 'Tube Test', "level_type" => 2],
            ['name' => 'Tunnel Test', "level_type" => 3],
            ['name' => 'Arm in cage Test', "level_type" => 4],
            ['name' => 'Cylinder Test', "level_type" => 5],
        ];

         foreach ($all_lab_tests as $type) {
            $type_created = \App\Models\Pro_LabTest::create([
                'lab_test_name' => $type['name'],
                'level_test' => $type['level_type'],
            ]);

            
        }
    }
}
