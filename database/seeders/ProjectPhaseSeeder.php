<?php

namespace Database\Seeders;

use App\Models\Pro_StudyActivity;
use App\Models\Pro_StudyPhase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectPhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $study_phases = [
            "Study Start"=>["Appointment of SD","Signature of protocol by SD"],
            "Planning Phase"=>["Signature of protocol by all parties"],
            "Experimental Phase"=>["First Experiment","Last Expriment"] ,
            "Report Phase"=>["Signature of final report by SD","Signature of final report by all parties"],
            "Archiving Phase"=>["Submission of study related documents to archivist"]
        ];


        foreach ($study_phases as $phase => $activities_tab) {
           

            $phase_created = Pro_StudyPhase::create([
                "phase_title"=>$phase
            ]);
            
            foreach ($activities_tab as $key => $activity) {
                # code...
                
                $activity_created = Pro_StudyActivity::create([
                    
                    "activity_title"=>$activity,
                    "phase_id"=>$phase_created->id,
                    "nb_days_min"=>10,
                    "nb_days_max"=>20,
                ]);
            }

        }


    }
}
