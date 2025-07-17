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
            "Study Start#Study Director appointment and drafting of protocol#Date of appointment of SD - See SD appointment form#Signature of protocol by SD - See copy of signed protocol#1#bg-primary"=>
            ["Appointment of SD","Signature of protocol by SD"],
            "Planning Phase#Study preparation,reception of test items etc.#Signature of protocol by all parties - See protocol approval page#Date of first experiment - See the first date at which data was recorded on raw data sheet#2#bg-success"=>["Signature of protocol by all parties"],
            "Experimental Phase#Study implementation and generation of data#Date of first experiment - See the first date at which data was recorded on raw data sheet#Date of last experiment - See the last date at which data was recorded on raw data sheet#3#bg-warning"=>["First Experiment","Last Expriment"] ,
            "Report Phase#Data analysis, Study Report drafting and signing#Date of last experiment - See the last date at which data was recorded on raw data sheet#Date of signature of final report by SD - See final report#4#bg-info"=>["Signature of final report by SD","Signature of final report by all parties"],
            "Archiving Phase#Preparation and submission of study related documents for archive#Date of signature of final report by all parties - See QA statement and Certificate of affirmation#Date study related documents are submitted to archivist - See archive deposit form and study checklist#5#bg-secondary"=>["Submission of study related documents to archivist"]
        ];


        foreach ($study_phases as $phase => $activities_tab) {
           

            $tab_phase_info = explode("#",$phase);

            $phase_created = Pro_StudyPhase::create([
                "phase_title"=>$tab_phase_info[0],
                "description"=>$tab_phase_info[1],
                "evidence1"=>$tab_phase_info[2],
                "evidence2"=>$tab_phase_info[3],
                "level"=>(int)$tab_phase_info[4],
                "class_couleur"=>$tab_phase_info[5],
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
