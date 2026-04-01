<?php

namespace Database\Seeders;

use App\Models\CpiaItem;
use App\Models\CpiaSection;
use Illuminate\Database\Seeder;

class CpiaSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'code' => 'A', 'letter' => 'A', 'sort_order' => 1,
                'title' => 'Whole Net cutting and washing',
                'items' => [
                    1 => 'Net labelling',
                    2 => 'Net cutting',
                    3 => 'Net washing procedure',
                    4 => 'Number of times nets are washed',
                    5 => 'Net storage',
                ],
            ],
            [
                'code' => 'B', 'letter' => 'B', 'sort_order' => 2,
                'title' => 'Net cutting and net labelling',
                'items' => [
                    1 => 'Number of whole nets to be cut and their labelling',
                    2 => 'Batch number selection',
                    3 => 'Number of net samples cutting',
                    4 => 'Net samples labelling',
                    5 => 'Storage of net samples',
                ],
            ],
            [
                'code' => 'C', 'letter' => 'C', 'sort_order' => 3,
                'title' => 'Evaluation of whole LLINs in Experimental huts',
                'items' => [
                    1  => 'Refurbishment of experimental huts',
                    2  => 'Training and consent of sleepers',
                    3  => 'Monitoring and recording of environmental condition of experimental huts',
                    4  => 'Drawing down curtains in experimental huts',
                    5  => 'Opening windows in experimental huts',
                    6  => 'Rotation of mosquito nets',
                    7  => 'Rotation of sleepers',
                    8  => 'Time spent by sleepers in experiment hut',
                    9  => 'Mosquito collection (Putting mosquitoes in correct cups)',
                    10 => 'Cleaning of experimental hut before each round',
                    11 => 'Mosquito transport conditions from experimental huts to laboratory',
                    12 => 'Decontamination of laboratory tools',
                    13 => 'Recording of mortality',
                    14 => 'Monitoring and recording of environmental condition in laboratory',
                ],
            ],
            [
                'code' => 'D', 'letter' => 'D', 'sort_order' => 4,
                'title' => 'Calibration of shaker bath and net washing',
                'items' => [
                    1 => 'Calibration of shaker bath',
                    2 => 'Number of net samples washed',
                    3 => 'Net sample washing procedure',
                    4 => 'Number of times nets are washed',
                    5 => 'Net samples storage',
                ],
            ],
            [
                'code' => 'E', 'letter' => 'E', 'sort_order' => 5,
                'title' => 'Cone Bioassay with LLIN samples',
                'items' => [
                    1 => 'Storage of mosquito net samples according to protocol',
                    2 => 'Decontamination of laboratory tools',
                    3 => 'Correct labelling of mosquito cage(s)',
                    4 => 'Acclimatization of mosquitoes to laboratory environment',
                    5 => 'Use of the right mosquito strain according to protocol',
                    6 => 'Number of mosquitoes exposed according to protocol',
                    7 => 'Contact time for mosquito exposure',
                    8 => 'Time for recording KD after exposure',
                    9 => 'Use of correct data sheet for recording of raw data',
                ],
            ],
            [
                'code' => 'F', 'letter' => 'F', 'sort_order' => 6,
                'title' => 'Cone Test on LLIN samples (Supplementary test)',
                'items' => [
                    1 => 'Decontamination of tools',
                    2 => 'Acclimatisation of mosquitoes to laboratory environment',
                    3 => 'Use of the right mosquito strain',
                    4 => 'Use of the right number of mosquitoes exposed according to protocol',
                    5 => 'Respect of contact time for mosquito exposure',
                    6 => 'Respect of KD recording time (1 hour post exposure)',
                    7 => 'Storage of net samples after test',
                    8 => 'Use of the right data sheet for recording of raw data',
                ],
            ],
            [
                'code' => 'G', 'letter' => 'G', 'sort_order' => 7,
                'title' => 'Tunnel Tests',
                'items' => [
                    1 => 'Decontamination of the tunnels',
                    2 => 'Presence of holes (created for mosquito passage) in the net samples during test',
                    3 => 'Acclimatization of mosquitoes to laboratory environment',
                    4 => 'Use of the right mosquito strain according to protocol',
                    5 => 'Use of the right animal according protocol',
                    6 => 'Right number of mosquitoes exposed according to protocol',
                    7 => 'Contact time for mosquito exposure',
                    8 => 'Use of the right data sheet for recording of raw data',
                ],
            ],
            [
                'code' => 'H', 'letter' => 'H', 'sort_order' => 8,
                'title' => 'Standard Mosquito Collection (SMC)',
                'items' => [
                    1  => 'Selection, training and consent of volunteer sleepers',
                    2  => 'Monitoring and recording of environmental condition of experimental huts',
                    3  => 'Rotation of sleepers',
                    4  => 'Rotation of methods',
                    5  => 'Time spent by sleepers in experiment hut',
                    6  => 'Mosquito transport conditions from experimental huts to laboratory',
                    7  => 'Recording of KD',
                    8  => 'Recording of immediate and delayed mortality',
                    9  => 'Cleaning of experimental hut',
                    10 => 'Monitoring and recording of environmental condition in laboratory',
                ],
            ],
            [
                'code' => 'I', 'letter' => 'I', 'sort_order' => 9,
                'title' => 'Human Landing Catches (HLC)',
                'items' => [
                    1  => 'Selection, training and consent of volunteer sleepers',
                    2  => 'Monitoring and recording of environmental condition of experimental huts',
                    3  => 'Rotation of sleepers',
                    4  => 'Rotation of methods',
                    5  => 'Time spent by sleepers in experiment hut',
                    6  => 'Mosquito transport conditions from experimental huts to laboratory',
                    7  => 'Recording of KD',
                    8  => 'Recording of immediate and delayed mortality',
                    9  => 'Cleaning of experimental hut',
                    10 => 'Monitoring and recording of environmental condition in laboratory',
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = CpiaSection::updateOrCreate(
                ['code' => $sectionData['code']],
                [
                    'letter'     => $sectionData['letter'],
                    'title'      => $sectionData['title'],
                    'sort_order' => $sectionData['sort_order'],
                    'is_active'  => true,
                ]
            );

            foreach ($sectionData['items'] as $num => $text) {
                CpiaItem::updateOrCreate(
                    ['section_id' => $section->id, 'item_number' => $num],
                    ['text' => $text, 'sort_order' => $num, 'is_active' => true]
                );
            }
        }
    }
}
