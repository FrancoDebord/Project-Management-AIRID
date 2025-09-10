<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $all_products_types= [
            ['name' => 'Insecticide Treated Nets (ITN)', "level_type" => 1],
            ['name' => 'Spatial Emanator (SE)', "level_type" => 2],
            ['name' => 'Attractive Targeted Sugar Bait (ATSB)', "level_type" => 3],
            ['name' => 'Indoor Resistance Sprays (IRS)', "level_type" => 4],
        ];

         foreach ($all_products_types as $type) {
            $type_created = \App\Models\Pro_ProductType::create([
                'product_type_name' => $type['name'],
                'level_product' => $type['level_type'],
            ]);

            
        }
    }
}
