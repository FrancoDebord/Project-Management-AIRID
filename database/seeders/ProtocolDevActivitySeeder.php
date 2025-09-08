<?php

namespace Database\Seeders;

use App\Models\Pro_ProtocolDevActivity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProtocolDevActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $list_activities= [
            "SD uploads Draft Protocol#1#Study Director#Project Manager#une_fois",
            "QA Inspection of Draft#2#Quality Assurance#Quality Assurance#plusieurs_fois",
            "Final Approved Protocol (signed)#3#Facility Manager#Study Director#une_fois",
            "QA Inspection of Final Protocol#4#Quality Assurance#Quality Assurance#plusieurs_fois",
            "Protocol Amendment / Deviation#5#Study Director#Project Manager#plusieurs_fois",
        ];

        foreach ($list_activities as $key => $protocol_dev_activity) {

            $tab_info = explode("#",$protocol_dev_activity);

            $act = Pro_ProtocolDevActivity::create([
                "nom_activite"=>$tab_info[0],
                "description_activite"=>$tab_info[0],
                "level_activite"=>$tab_info[1],
                "staff_role_perform"=>$tab_info[2],
                "alternative_staff_role_perform"=>$tab_info[3],
                "multipicite"=>$tab_info[4],
            ]);
            # code...
        }
    }
}
