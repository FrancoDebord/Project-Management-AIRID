<?php

namespace Database\Seeders;

use App\Models\Pro_Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $project_codes = [
            "24-01","24-02","24-03","24-04-A/GLP","24-04-B/GLP",
            "24-05","24-06/GLP","24-07","24-08","24-09","24-10","24-11","24-12",
            "25-01","25-02/GLP"
        ];

        foreach ($project_codes as $key => $code_project) {
            # code...

            $new_project = Pro_Project::create([
                "project_code"=>$code_project,
                "project_title"=>$code_project,
                "protocol_code"=>"P/".$code_project,
                "study_director"=>1,
                "date_debut_previsionnelle"=>date("Y-m-d"),
                "date_debut_effective"=>date("Y-m-d"),
                "date_fin_previsionnelle"=>date("Y-m-d"),
                "date_fin_effective"=>date("Y-m-d"),
                "project_stage"=>"in progress",
            ]);
        }
    }
}
