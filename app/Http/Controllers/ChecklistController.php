<?php

namespace App\Http\Controllers;

use App\Models\Pro_QaInspection;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    /**
     * Registre des 13 formulaires de checklist.
     * Clé = slug URL, valeur = [model class, title, letter, questions[]]
     */
    private static function forms(): array
    {
        return [
            'cone-llin' => [
                'model'  => \App\Models\Pro_Cl_ConeLlin::class,
                'letter' => 'A',
                'title'  => 'Cone Bioassay with LLIN samples',
                'questions' => [
                    1  => 'Was decontamination of laboratory tools performed?',
                    2  => 'Is the shaker bath calibrated at 155 rounds per minutes, at the temperature of 30°C during washing for 10 min?',
                    3  => 'Are mosquito net samples stored at 30°C in the incubator?',
                    4  => 'Are mosquito net sample stored according to protocol?',
                    5  => 'Is the test system (mosquitoes) available for test?',
                    6  => 'Is/ Are the mosquito cage(s) labelled accordingly?',
                    7  => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    8  => 'Are mosquitoes used for test of recommended age or according to protocol?',
                    9  => 'Is the mosquito strain used according to protocol?',
                    10 => 'Is the number of mosquitoes exposed according to protocol?',
                    11 => 'Is the time of contact respected?',
                    12 => 'Is the KD recorded exactly 1 hour after time of contact?',
                    13 => 'Are net samples stored at correct temperature and according to protocol after use?',
                ],
            ],

            'cone-irs-blocks-treatment' => [
                'model'  => \App\Models\Pro_Cl_ConeIrsBlTreat::class,
                'letter' => 'B',
                'title'  => 'Cone Bioassay with IRS blocks (Blocks treatment)',
                'questions' => [
                    1  => 'Was the potter tower calibrated before impregnation?',
                    2  => 'Are the balances used calibrated?',
                    3  => 'Was the insecticide stored at the correct temperature?',
                    4  => 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    5  => 'Was the fume hood used for mixture of insecticide dilutions?',
                    6  => 'Was the appropriate PPE (coat, mask, protection glasses etc.) followed?',
                    7  => 'Are the blocks used of same age?',
                    8  => 'Are the blocks used for spraying within the acceptable range of pH?',
                    9  => 'Are the blocks properly labelled?',
                    10 => 'Are the treated blocks stored at the correct temperature?',
                ],
            ],

            'cone-irs-blocks-test' => [
                'model'  => \App\Models\Pro_Cl_ConeIrsBlTest::class,
                'letter' => 'C',
                'title'  => 'Cone Bioassay with IRS blocks (Test)',
                'questions' => [
                    1  => 'Are all blocks used for testing within the acceptable ranges?',
                    2  => 'Are laboratory tools decontaminated before use?',
                    3  => 'Is the environmental condition of the laboratory monitored and recorded and according to protocol?',
                    4  => 'Is the number of mosquitoes exposed according to Protocol?',
                    5  => 'Is the correct mosquito strain being used?',
                    6  => 'Are the mosquitoes used of the age specified by the protocol?',
                    7  => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    8  => 'Is the time of contact respected?',
                    9  => 'Is the KD recorded exactly 1 hour after time of contact?',
                    10 => 'Is the correct data sheet used?',
                    11 => 'Are IRS blocks stored at correct temperature after use?',
                ],
            ],

            'tunnel-test' => [
                'model'  => \App\Models\Pro_Cl_TunnelTest::class,
                'letter' => 'D',
                'title'  => 'Tunnel Test',
                'questions' => [
                    1  => 'Are tunnels decontaminated before use?',
                    2  => 'Are laboratory tools decontaminated before use?',
                    3  => 'Where each netting pieces with 9 holes of 1 cm in diameter each?',
                    4  => 'Are mosquitoes used (strain and age) for test according to protocol?',
                    5  => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    6  => 'Is the correct animal used? According Protocol?',
                    7  => 'Are mosquitoes and animals inserted in the correct compartment of the tunnels?',
                    8  => 'Is test performed in recommended environmental conditions?',
                    9  => 'Is the number of mosquitoes exposed according to Protocol?',
                    10 => 'Are net samples stored at correct temperature after use?',
                    11 => 'Is the time of contact respected?',
                    12 => 'Is the correct data sheet used?',
                    13 => 'Is blood feeding and immediate mortality scored?',
                    14 => 'Are mosquitoes placed in appropriate holding cups and are holding cups labelled appropriately?',
                    15 => 'Are mosquitoes provided access to 10% glucose solution?',
                    16 => 'Are net samples used for tunnel tests wrapped and stored according to protocol?',
                ],
            ],

            'llin-washing' => [
                'model'  => \App\Models\Pro_Cl_LlinWashing::class,
                'letter' => 'E',
                'title'  => 'Evaluation of Whole LLIN in Experimental huts (Washing and Cutting of Whole Nets)',
                'questions' => [
                    1  => 'Are Whole LLIN clearly labelled?',
                    2  => 'Are Whole LLIN cut as described in the protocol?',
                    3  => 'Are net samples cut for laboratory bioassays and chemical analysis respectively?',
                    4  => 'Is the number of net samples cut from the whole LLINs according to protocol?',
                    5  => 'Are net samples cut wrapped and labelled appropriately?',
                    6  => 'Are net samples stored according to protocol?',
                    7  => 'Is the time for washing whole net respected?',
                    8  => 'Is the washing procedure given in the protocol respected?',
                    9  => 'Are whole LLIN allowed to dry in recommended conditions (in the shade)?',
                    10 => 'Are whole LLIN stored in recommended conditions?',
                ],
            ],

            'llin-exp-huts' => [
                'model'  => \App\Models\Pro_Cl_LlinExpHuts::class,
                'letter' => 'F',
                'title'  => 'Evaluation of Whole LLIN in Experimental huts',
                'questions' => [
                    1  => 'Is a signed copy of the protocol available at the field site?',
                    2  => 'Were the experimental huts refurbished before start of evaluation?',
                    3  => 'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    4  => 'Was the consent form signed by all the sleeper?',
                    5  => 'Was a sleeper\'s rotation plan established?',
                    6  => 'Was a mosquito net rotation plan established?',
                    7  => 'Are the curtains lifted up in all the experimental huts?',
                    8  => 'Were the slits opened in all the experimental huts?',
                    9  => 'Were the sleepers in experimental hut for 9 hours?',
                    10 => 'Was a surprise visit performed?',
                    11 => 'Are mosquitoes nets rotated according to protocol?',
                    12 => 'Are sleepers rotated every day?',
                    13 => 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    14 => 'Are the experimental huts cleaned before each round?',
                    15 => 'Were mosquitoes transported to laboratory in recommended conditions?',
                    16 => 'Were laboratory tools decontaminated?',
                    17 => 'Was the mortality recorded after 24 hours after and according to protocol?',
                    18 => 'Is the laboratory environmental condition monitored, recorded and according to protocol?',
                    19 => 'Were incidents (major or minor) reported accordingly?',
                    20 => 'Are all raw data sheets signed by the Study Director?',
                ],
            ],

            'irs-treatment' => [
                'model'  => \App\Models\Pro_Cl_IrsTreatment::class,
                'letter' => 'G',
                'title'  => 'IRS Treatment application',
                'questions' => [
                    1  => 'Is a signed copy of the protocol available at the field site?',
                    2  => 'Were the experimental huts refurbished before start of evaluation?',
                    3  => 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    4  => 'Was the sprayer cleaned and decontaminated?',
                    5  => 'Was the Hudson Sprayer calibrated before use?',
                    6  => 'Was an absorbent paper fixed on the window slits and at the edges of the floor closest to the wall to avoid contamination of entry points and to pick any run off of insecticide from the walls during spraying?',
                    7  => 'Were the hut walls pre-marked with swatts?',
                    8  => 'Were filter papers fixed on the walls?',
                    9  => 'Is the lance speed respected?',
                    10 => 'Was the correct PPE used?',
                    11 => 'Is the pressure checked and sprayer pumped back after spraying each wall or hut?',
                    12 => 'After spraying, was the spray tank depressurised and the volume of insecticide solution left in the tank measured and recorded?',
                    13 => 'Was the sprayer washed properly and according to SOP?',
                ],
            ],

            'irs-trial' => [
                'model'  => \App\Models\Pro_Cl_IrsTrial::class,
                'letter' => 'H',
                'title'  => 'IRS Trial',
                'questions' => [
                    1  => 'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    2  => 'Was the consent form signed by all the sleepers?',
                    3  => 'Was a sleeper\'s rotation plan established?',
                    4  => 'Are sleepers rotated every day?',
                    5  => 'Were the sleepers provided each with a torch, an aspirator, a broom, a container for urinating and labelled plastic cups?',
                    6  => 'Were the windows opened, the curtain lifted and the door closed?',
                    7  => 'Were the sleepers in experimental hut from 9pm and 6 am?',
                    8  => 'Were mosquitoes collected in the evening and in the morning?',
                    9  => 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    10 => 'Were captured mosquitoes transported to laboratory in recommended conditions?',
                    11 => 'Was a surprise visit performed?',
                    12 => 'Are the experimental huts cleaned before each round?',
                    13 => 'Is the correct data sheet used?',
                    14 => 'Are mosquitoes preserved in recommended conditions for analysis?',
                ],
            ],

            'cone-irs-walls' => [
                'model'  => \App\Models\Pro_Cl_ConeIrsWalls::class,
                'letter' => 'I',
                'title'  => 'Cone Bioassay on IRS treated walls',
                'questions' => [
                    1  => 'Was each wall numbered including the ceiling?',
                    2  => 'Were the mosquito holding cups labelled according to corresponding wall?',
                    3  => 'Are mosquitoes used for test according to protocol?',
                    4  => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    5  => 'Were testing tools decontaminated?',
                    6  => 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    7  => 'Is the number of mosquitoes exposed according to Protocol?',
                    8  => 'Is the time of contact respected?',
                    9  => 'Was honey juice given to the mosquitoes?',
                    10 => 'Is the KD recorded exactly 1 hour after time of contact?',
                    11 => 'Is the correct data sheet used?',
                    12 => 'Were mosquitoes transported to laboratory in recommended conditions?',
                ],
            ],

            'cylinder-bioassay' => [
                'model'  => \App\Models\Pro_Cl_CylinderBioassay::class,
                'letter' => 'J',
                'title'  => 'Cylinder Bioassay',
                'questions' => [
                    1 => 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    2 => 'Are mosquitoes used for test according to protocol?',
                    3 => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    4 => 'Is the number of mosquitoes exposed according to Protocol?',
                    5 => 'Is the time of contact respected?',
                    6 => 'Is the KD recorded exactly 1 hour after time of contact?',
                    7 => 'Is the correct data sheet used?',
                    8 => 'Are mosquitoes preserved in proper conditions after testing?',
                ],
            ],

            'cdc-bottle-coating' => [
                'model'  => \App\Models\Pro_Cl_CdcBottleCoating::class,
                'letter' => 'K',
                'title'  => 'CDC Bottle Bioassay (Coating)',
                'questions' => [
                    1 => 'Were CDC bottles washed before use?',
                    2 => 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    3 => 'Was the stock solution stored in the refrigerator at 4°C?',
                    4 => 'Were the CDC bottles labelled accordingly?',
                    5 => 'Was the stock solution, after being removed from the refrigerator, left at room temperature for 1–2 hours before use?',
                    6 => 'Were the CDC bottles allowed to rotate for 15 minutes on the tube roller?',
                    7 => 'Were the CDC bottles kept in a dark place after coating?',
                    8 => 'Were the CDC bottles allow to dry after coating?',
                ],
            ],

            'cdc-bottle-test' => [
                'model'  => \App\Models\Pro_Cl_CdcBottleTest::class,
                'letter' => 'L',
                'title'  => 'CDC Bottle Bioassay (Test)',
                'questions' => [
                    1  => 'Is testing done 24 hours after coating?',
                    2  => 'Is the correct mosquito strain being used?',
                    3  => 'Are mosquitoes of the age specified by protocol?',
                    4  => 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    5  => 'Is the number of mosquitoes exposed according to Protocol?',
                    6  => 'Is the time of exposure according to protocol?',
                    7  => 'Is the time of contact respected?',
                    8  => 'Is the KD recorded according to protocol?',
                    9  => 'Is the correct data sheet used?',
                    10 => 'Is the delayed mortality recorded according to protocol?',
                ],
            ],

            'spatial-repellents' => [
                'model'  => \App\Models\Pro_Cl_SpatialRepellents::class,
                'letter' => 'M',
                'title'  => 'Evaluation of spatial repellents in Experimental huts',
                'questions' => [
                    1  => 'Is a signed copy of the protocol available at the field site?',
                    2  => 'Were the experimental huts refurbished before start of evaluation?',
                    3  => 'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    4  => 'Was the consent form signed by all the sleeper?',
                    5  => 'Was a sleeper\'s rotation plan established?',
                    6  => 'Was a treatment rotation plan established?',
                    7  => 'Are the curtains lifted up in all the experimental huts?',
                    8  => 'Were the slits opened in all the experimental huts?',
                    9  => 'Were the sleepers in experimental hut for 9 hours?',
                    10 => 'Was a surprise visit performed?',
                    11 => 'Are treatments rotated according to protocol?',
                    12 => 'Are sleepers rotated every day?',
                    13 => 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    14 => 'Are the experimental huts cleaned before each round?',
                    15 => 'Were mosquitoes transported to laboratory in recommended conditions?',
                    16 => 'Were laboratory tools decontaminated?',
                    17 => 'Was the mortality recorded after 24 hours after and according to protocol?',
                    18 => 'Is the laboratory environmental condition monitored, recorded and according to protocol?',
                    19 => 'Were incidents (major or minor) reported accordingly?',
                    20 => 'Are all raw data sheets signed by the Study Director?',
                ],
            ],
        ];
    }

    /**
     * Index : liste des 13 formulaires pour une inspection donnée.
     */
    public function index(int $inspection_id)
    {
        $inspection = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        $forms      = self::forms();

        // Pour chaque formulaire, vérifier si un enregistrement existe
        $statuses = [];
        foreach ($forms as $slug => $form) {
            $modelClass       = $form['model'];
            $statuses[$slug]  = $modelClass::where('inspection_id', $inspection_id)->exists();
        }

        return view('checklists.index', compact('inspection', 'forms', 'statuses'));
    }

    /**
     * Show : affiche le formulaire d'un checklist.
     */
    public function show(int $inspection_id, string $slug)
    {
        $forms = self::forms();

        abort_if(!isset($forms[$slug]), 404, 'Formulaire introuvable.');

        $inspection = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        $form       = $forms[$slug];
        $modelClass = $form['model'];
        $record     = $modelClass::where('inspection_id', $inspection_id)->first();

        return view('checklists.form', compact('inspection', 'slug', 'form', 'record'));
    }

    /**
     * Save : sauvegarde (ou met à jour) les réponses d'un formulaire.
     */
    public function save(Request $request, int $inspection_id, string $slug)
    {
        $forms = self::forms();
        abort_if(!isset($forms[$slug]), 404, 'Formulaire introuvable.');

        $inspection = Pro_QaInspection::findOrFail($inspection_id);
        $form       = $forms[$slug];
        $modelClass = $form['model'];
        $questions  = $form['questions'];

        // Construire le tableau des réponses
        $data = [
            'project_id'   => $inspection->project_id,
            'project_code' => $inspection->project?->project_code,
            'inspection_id'=> $inspection_id,
            'comments'     => $request->input('comments'),
            'filled_by'    => auth()->id() ?? null,
        ];

        foreach (array_keys($questions) as $n) {
            $data['q' . $n] = $request->input('q' . $n);
        }

        $modelClass::updateOrCreate(
            ['inspection_id' => $inspection_id],
            $data
        );

        return redirect()
            ->route('checklist.index', $inspection_id)
            ->with('success', 'Formulaire "' . $form['letter'] . '. ' . $form['title'] . '" enregistré avec succès.');
    }

    /**
     * Statuses AJAX : retourne le statut (rempli/non) de chaque formulaire pour une inspection.
     */
    public function statuses(Request $request)
    {
        $inspection_id = $request->integer('inspection_id');
        abort_if(!$inspection_id, 400);

        $forms    = self::forms();
        $statuses = [];

        foreach ($forms as $slug => $form) {
            $modelClass      = $form['model'];
            $statuses[$slug] = $modelClass::where('inspection_id', $inspection_id)->exists();
        }

        return response()->json([
            'success'  => true,
            'statuses' => $statuses,
        ]);
    }
}
