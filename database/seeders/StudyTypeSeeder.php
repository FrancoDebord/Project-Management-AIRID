<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $study_types = [
            ['name' => 'Lab Study', "level_type" => 1],
            ['name' => 'Hut Trials', "level_type" => 2],
            ['name' => 'Community Trials', "level_type" => 3],
        ];

        $study_sub_categories = [
            'Lab Study' => [
                ["study_sub_category_name" => "ITN Evaluation", "level_sub_category" => 1],
                ["study_sub_category_name" => "IRS Evaluation", "level_sub_category" => 2],
                ["study_sub_category_name" => "Resistance Testing", "level_sub_category" => 3],
            ],
            'Hut Trials' => [

                ["study_sub_category_name" => "ITN Evaluation", "level_sub_category" => 1],
                ["study_sub_category_name" => "IRS Evaluation", "level_sub_category" => 2],
                ["study_sub_category_name" => "Spatial Repellents", "level_sub_category" => 3],
                ["study_sub_category_name" => "Other Products", "level_sub_category" => 4],
            ],
            'Community Trials' => [
                ["study_sub_category_name" => "ITN Phase 3", "level_sub_category" => 1],
                ["study_sub_category_name" => "IRS Phase 3", "level_sub_category" => 2],
                ["study_sub_category_name" => "Randomised Control Trials", "level_sub_category" => 3],
                ["study_sub_category_name" => "Other Trials", "level_sub_category" => 4],
            ],
        ];


        foreach ($study_types as $type) {
            $type_created = \App\Models\Pro_StudyType::create([
                'study_type_name' => $type['name'],
                'level_type' => $type['level_type'],
            ]);

            if (array_key_exists($type['name'], $study_sub_categories)) {
                foreach ($study_sub_categories[$type['name']] as $sub_category_name) {
                    \App\Models\Pro_StudyTypeSubCategory::create([
                        'study_sub_category_name' => $sub_category_name['study_sub_category_name'],
                        'level_sub_category' => $sub_category_name['level_sub_category'],
                        'study_type_id' => $type_created->id,
                    ]);
                }
            }
        }
    }
}
