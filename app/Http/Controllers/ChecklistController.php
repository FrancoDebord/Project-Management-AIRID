<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\DocumentSignature;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_QaInspection;
use App\Models\InspectionSnapshot;
use App\Models\User;
use App\Services\ChecklistSnapshotService;
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
     * Amendment/Deviation Inspection Checklist (QA-PR-1-004/06).
     * Single section, 8 YES/NO/NA questions + extra header fields.
     * Used for both "Study Protocol Amendment/Deviation Inspection"
     * and "Study Report Amendment Inspection".
     */
    private static function amendmentDeviationForm(): array
    {
        return [
            'model'   => \App\Models\Pro_Cl_AmendmentDeviationInspection::class,
            'letter'  => 'AD',
            'title'   => 'Amendment & Deviations',
            'doc_ref' => 'QA-PR-1-004/06',
            'questions' => [
                1 => 'Is there an amendment/ deviation N°',
                2 => 'Is the number of pages over the total number of pages visible?',
                3 => 'Is the study code written?',
                4 => 'Is the study title stated?',
                5 => 'Was the amendment/ deviation described appropriately?',
                6 => 'Is the reason for the amendment/ deviation described stated?',
                7 => 'Is the impact on the study described?',
                8 => 'Was the amendment/ deviation signed by the Study Director?',
            ],
        ];
    }

    /** Returns true if the type uses the amendment/deviation form. */
    private static function isAmendmentType(string $type): bool
    {
        return in_array($type, [
            'Study Protocol Amendment/Deviation Inspection',
            'Study Report Amendment Inspection',
        ]);
    }

    /**
     * Study Protocol Inspection — 6 sections (A–F).
     */
    private static function studyProtocolForms(): array
    {
        $model = \App\Models\Pro_Cl_StudyProtocolInspection::class;
        return [
            'sp-a' => [
                'model'   => $model,
                'section' => 'a',
                'letter'  => 'A',
                'title'   => 'General',
                'questions' => [
                    1  => 'Is the Study code provided?',
                    2  => 'Is the Study title stated?',
                    3  => 'Is the page number on each page and the total number of pages in the document?',
                    4  => 'Are Study purpose/objectives clearly stated?',
                    5  => 'Are the Sponsor name and address provided?',
                    6  => 'Are Testing facility name and address provided?',
                    7  => 'Is the Identity of study participants/test sites complete?',
                    8  => 'Is the Proposed start date included?',
                    9  => 'Is the Proposed termination date included?',
                    10 => 'Is the Name of Study Director, Principal Investigator (where applicable) included?',
                    11 => 'Is there a space for the Study Director, Management, the Principal Investigator (where applicable) and the Sponsor to sign and approve protocol?',
                    12 => 'Are records to be maintained listed?',
                    13 => 'Is the archive location of the list of records to be retained and length of time for which records will be retained stated?',
                    14 => 'Is reference to test guideline(s) and/or method(s) used (where applicable)?',
                    15 => 'Is the description of any acceptance criteria that must be fulfilled for the study to be considered to be valid (if applicable) stated?',
                    16 => 'Is there a statement stating that the study is to GLP or not?',
                    17 => 'Is there a space to show that the QAU Manager has verified the protocol?',
                    18 => 'Are the procedures for the production of amendments and deviations described?',
                    19 => 'Is a distribution list of the protocol included?',
                    20 => 'Is the study timeline included?',
                ],
            ],
            'sp-b' => [
                'model'   => $model,
                'section' => 'b',
                'letter'  => 'B',
                'title'   => 'Test System',
                'questions' => [
                    1 => 'Is the Test system described?',
                    2 => 'Is the Source of test system stated?',
                    3 => 'Are the Characteristics/status of test system described?',
                    4 => 'Is the number of test system defined?',
                ],
            ],
            'sp-c' => [
                'model'   => $model,
                'section' => 'c',
                'letter'  => 'C',
                'title'   => 'Test, Control & Reference Articles',
                'questions' => [
                    1 => 'Is the Name, CAS number or code number provided?',
                    2 => 'Is the Supplier of test, control & ref. substance stated?',
                    3 => 'Are Storage conditions information for the test, control, ref. substance stated?',
                    4 => 'Is the Carrier or vehicle identified?',
                    5 => 'Is the Concentration of test material stated?',
                    6 => 'Is the frequency of applications stated?',
                ],
            ],
            'sp-d' => [
                'model'   => $model,
                'section' => 'd',
                'letter'  => 'D',
                'title'   => 'Equipment',
                'questions' => [
                    1 => 'Are the Equipment needed for study available?',
                    2 => 'Are Equipment calibrated and well maintained?',
                ],
            ],
            'sp-e' => [
                'model'   => $model,
                'section' => 'e',
                'letter'  => 'E',
                'title'   => 'SOPs',
                'questions' => [
                    1 => 'Are SOPs to be used listed in the study protocol?',
                    2 => 'Are the SOPs to be used in the study available?',
                    3 => 'Have the SOPs been approved?',
                    4 => 'Have the study personnel read, understood the SOPs to be used in the study?',
                ],
            ],
            'sp-f' => [
                'model'   => $model,
                'section' => 'f',
                'letter'  => 'F',
                'title'   => 'Study Personnel',
                'type'    => 'study_personnel', // special rendering
                'questions' => [
                    1 => 'Are study personnel appointed for study sufficient?',
                ],
                'staff_count' => 15,
            ],
        ];
    }

    /**
     * Study Report Inspection — 5 sections (A–E).
     */
    private static function studyReportForms(): array
    {
        $model = \App\Models\Pro_Cl_StudyReportInspection::class;
        return [
            'sr-a' => [
                'model'    => $model,
                'section'  => 'a',
                'letter'   => 'A',
                'title'    => 'General',
                'doc_ref'  => 'QA-PR-1-005/06',
                'questions' => [
                    1  => 'Is the Project code provided?',
                    2  => 'Is the Protocol code provided?',
                    3  => 'Is the Study title stated?',
                    4  => 'Is the Study director clearly identified?',
                    5  => 'Is the Study Report date stated?',
                    6  => 'Is the number on each page and the total number of pages visible in the entire document?',
                    7  => 'Is the Name and address of testing facility provided?',
                    8  => 'Is the Name and address of sponsor provided?',
                    9  => 'Is there a table of content?',
                    10 => 'Is there a list of annexes?',
                    11 => 'Is there a list of tables?',
                    12 => 'Is there a list of figures?',
                    13 => 'Is there a signed SD Statement on GLP compliance included in the report?',
                    14 => 'Is the certificate of affirmation issued by the FM dated, signed and included in the report?',
                    15 => 'Is there a copy of GLP accreditation certificate included?',
                    16 => 'Is the study start date and end date included?',
                    17 => 'Are references guidelines enumerated?',
                    18 => 'Are the Names of all scientists and key personnel involved mentioned?',
                    19 => 'Were the tests performed during the study, test procedures and outcome measures mentioned?',
                    20 => 'Is the list of SOPs used during the study enumerated?',
                    21 => 'Are Protocol amendments/deviations all signed and made available in the study report?',
                    22 => 'Is the list of equipment used during the study and their respective codes enumerated?',
                    23 => 'Is there a list of acronyms?',
                    24 => 'Are all study related documents archived, listed and available?',
                    25 => 'Are all pages of the final report clear and readable?',
                    26 => 'Is the description of all circumstances affecting the quality of integrity of the data (major and minor incident) mentioned?',
                    27 => 'Does the report contain all details necessary to summarize the study procedures and conclusions?',
                    28 => 'Is there a distribution list of Study report?',
                ],
            ],
            'sr-b' => [
                'model'    => $model,
                'section'  => 'b',
                'letter'   => 'B',
                'title'    => 'Test, Control and Reference Substances',
                'doc_ref'  => 'QA-PR-1-005/06',
                'questions' => [
                    1 => 'Is the Name, CAS or code number of test and control or reference substance mentioned in the final report?',
                    2 => 'Is the supplier of test and control or reference substance stated?',
                    3 => 'Are storage conditions information for test and control or reference substance monitored and mentioned?',
                ],
            ],
            'sr-c' => [
                'model'    => $model,
                'section'  => 'c',
                'letter'   => 'C',
                'title'    => 'Test System Description',
                'doc_ref'  => 'QA-PR-1-005/06',
                'questions' => [
                    1 => 'Are the following described about the test system? (a-Strain, b-Age, c-Source, d-Resistance status, e-Number)',
                    2 => 'Is the test condition (temperature) mentioned?',
                ],
            ],
            'sr-d' => [
                'model'    => $model,
                'section'  => 'd',
                'letter'   => 'D',
                'title'    => 'Data Management and Statistical Analysis',
                'doc_ref'  => 'QA-PR-1-005/06',
                'questions' => [
                    1 => 'Is there evidence of the validation of the software used for data entry and is it included in the final report?',
                    2 => 'Are all data double entered?',
                    3 => 'Are all relevant raw data reported and omissions (if applicable) explained?',
                    4 => 'Is there a description of: a-Summary of analysis of data, b-Conclusions drawn from data',
                ],
            ],
            'sr-e' => [
                'model'    => $model,
                'section'  => 'e',
                'letter'   => 'E',
                'title'    => 'Quality Assurance',
                'doc_ref'  => 'QA-PR-1-005/06',
                'questions' => [
                    1 => 'Are all QA audit/inspection reports complete with findings addressed?',
                    2 => 'Was a QA statement issued and included in the report?',
                    3 => 'Is a Quality Assurance statement accurate, complete, signed and dated by the QA Manager?',
                    4 => 'Is the QA file for the Study complete?',
                ],
            ],
        ];
    }

    /**
     * Data Quality Inspection — 5 sections (A–E).
     * QA-PR-1-018/03
     */
    private static function dataQualityForms(): array
    {
        $model = \App\Models\Pro_Cl_DataQualityInspection::class;
        return [
            'dq-a' => [
                'model'      => $model,
                'section'    => 'a',
                'letter'     => 'A',
                'title'      => 'Staff Training',
                'doc_ref'    => 'QA-PR-1-018/03',
                'form_type'  => 'dq_standard',
                'has_header' => true,
                'questions'  => [
                    '1' => 'Are all staff trained on GLP?',
                    '2' => 'Are all key study personnel trained on Data Management?',
                    '3' => 'Are all key study personnel trained on study related activities?',
                ],
            ],
            'dq-b' => [
                'model'     => $model,
                'section'   => 'b',
                'letter'    => 'B',
                'title'     => 'Computerised Systems and Softwares Validation',
                'doc_ref'   => 'QA-PR-1-018/03',
                'form_type' => 'dq_standard',
                'questions' => [
                    '1'  => 'Are computerised systems (Computers, data loggers etc.) to be used for GLP study clearly identified?',
                    '2'  => 'Are softwares (Excel, Stata etc.) to be used during GLP study clearly identified?',
                    '3'  => 'Are computerised systems validated before study start date?',
                    '4'  => 'Are softwares validated before study start date?',
                    '5'  => 'Is the maintenance of computerised systems used for study up to date?',
                    '6'  => 'Are all computers to be used equipped with an up-to-date anti-virus programme?',
                    '7'  => 'Was a computerised system risk assessment performed for study?',
                    '8'  => 'Is the Data-base established before study start date?',
                    '9'  => 'Is the Data-base approved by Study Director and Facility Manager?',
                    '10' => 'Is the study code clearly identified?',
                    '11' => 'Is / Are the type(s) of test(s) clearly stated?',
                    '12' => 'Has there been any amendment made to the database since first validation?',
                ],
            ],
            'dq-c' => [
                'model'     => $model,
                'section'   => 'c',
                'letter'    => 'C',
                'title'     => 'Data Validity',
                'doc_ref'   => 'QA-PR-1-018/03',
                'form_type' => 'dual_verification',
                'questions' => [
                    '1'  => 'Are expected outcome measures (Knock-down, mortality, passage rate, blood feeding, blood feeding inhibition, hut entry or deterrence etc.) clearly stated',
                    '2'  => 'Are test validity criteria clearly defined?',
                    '3'  => 'Is there a description of all circumstances affecting the quality of integrity of Data (major and minor incidents)?',
                    '4'  => 'Are test performed valid? (Check if KD or mortality etc. in controls are not inferior to acceptable range, check if environmental conditions are all within required range)',
                    '5'  => 'Was the first data entry performed and signed?',
                    '6'  => 'Are data received by data management verified to ensure they are not corrupted during transfer? Was a data verification conducted by data management?',
                    '7'  => 'Was a second data entry performed and signed?',
                    '8'  => 'Are there any issues raised from data verification performed?',
                    '9'  => 'Was there any data retrieved from server?',
                    '10' => 'Is the procedure for request for data retrieval from server respected?',
                ],
            ],
            'dq-d' => [
                'model'      => $model,
                'section'    => 'd',
                'letter'     => 'D',
                'title'      => 'Data Sheet Information',
                'doc_ref'    => 'QA-PR-1-018/03',
                'form_type'  => 'dual_verification',
                'sub_items'  => ['2a','2b','2c','2d','2e','2f','2g','2h','2i','2j','2k','2l','2m','2n'],
                'questions'  => [
                    '1'  => 'Is the correct data sheet used for each type of test or to record raw data?',
                    '2'  => 'Is the information on each data sheet complete?',
                    '2a' => 'a. Is the heading completely filled?',
                    '2b' => 'b. Is the date at which data was recorded clearly stated?',
                    '2c' => 'c. Is the study code clearly identified?',
                    '2d' => 'd. Is the mosquito strain clearly identified?',
                    '2e' => 'e. Is the mosquito age clearly identified?',
                    '2f' => 'f. Is the environmental condition clearly identified?',
                    '2g' => 'g. Are data recorded directly, legibly and indelibly?',
                    '2h' => 'h. Are each data sheet verified by Supervisor and/or Study Director?',
                    '2i' => 'i. Are each data sheet signed by the Supervisor and/or Study Director?',
                    '2j' => 'j. Are error resolution procedures for data respected?',
                    '2k' => 'k. Are alterations to data (due to wrong entry) such that they do not obscure the original and indicate the person making the alteration, the date of alteration and the reason for alteration using the appropriate error correction code where appropriate?',
                    '2l' => 'l. Are there data missing?',
                    '2m' => 'm. Are there repeated data?',
                    '2n' => 'n. Are there incorrect data?',
                    '3'  => 'Are all raw data organised?',
                    '4'  => 'Are all raw data complete?',
                ],
            ],
            'dq-e' => [
                'model'     => $model,
                'section'   => 'e',
                'letter'    => 'E',
                'title'     => 'Study Box',
                'doc_ref'   => 'QA-PR-1-018/03',
                'form_type' => 'study_box',
                'questions' => [
                    '1'  => 'Is there a SD appointment form?',
                    '2'  => 'Is there a copy of the study protocol?',
                    '3'  => 'Is there a copy of the study initiation meeting minutes with SD, study personnel and safety officer?',
                    '4'  => 'Are there certificates of analysis of test items?',
                    '5'  => 'Acknowledgement of receipt of test items?',
                    '6'  => 'Is there a folder for decontamination sheets?',
                    '7'  => 'Is there a folder for material safety data sheets?',
                    '8'  => 'Is there a folder for ethical approval documents?',
                    '9'  => 'Is there a folder for consent forms?',
                    '10' => 'Is there a folder for the selection and training of volunteer sleepers?',
                    '11' => 'Is there a folder for study participant information sheets?',
                    '12' => 'Is there a copy of the risk assessment performed by safety officer?',
                    '13' => 'Is there a copy of software and program validation documents?',
                    '14' => 'Is there a folder for each type of test performed?',
                    '15' => 'Is there a copy of treatment and sleepers rotation plan?',
                    '16' => 'Is there a copy of net washing calendar?',
                    '17' => 'Is there a folder for amendments or deviations?',
                    '18' => 'Is there a folder for material transfer sheets?',
                    '19' => 'Is there a folder for records of procedures sheets?',
                    '20' => 'Is there a folder for net cutting sheets?',
                    '21' => 'Is there a folder for collection of mosquitoes in experimental huts sheets?',
                    '22' => 'Is there a folder for surprise visit checklist?',
                    '23' => 'Is there a folder for cleaning of experimental huts checklists?',
                    '24' => 'Is there a folder for animals\' related documents?',
                    '25' => 'Is there a folder for test item transport condition forms?',
                    '26' => 'Is there a study director activity checklist?',
                    '27' => 'Is the project journal available?',
                    '28' => 'Are there any documents available? If yes… List them.',
                    '29' => 'Are all documents signed appropriately and blank spaces filled up?',
                    '30' => 'Is there a copy of the Study final report?',
                ],
            ],
        ];
    }

    /**
     * Index : liste des formulaires pour une inspection donnée.
     * Pour les Facility Inspections, affiche les 15 sections avec progression.
     */
    public function index(int $inspection_id)
    {
        $inspection = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);

        if ($inspection->date_scheduled && now()->toDateString() < $inspection->date_scheduled) {
            return redirect('/project/create?project_id=' . $inspection->project_id . '#step6')
                ->with('error', 'Cette inspection ne peut pas être remplie avant sa date prévue (' . \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') . ').');
        }

        // ── Snapshot-based system ────────────────────────────────────────────
        if (InspectionSnapshot::where('inspection_id', $inspection_id)->exists()) {
            $forms        = ChecklistSnapshotService::allForms($inspection);
            $doneSlugs    = ChecklistSnapshotService::doneSlugs($inspection_id);
            $conformities = ChecklistSnapshotService::conformities($inspection_id);
            $total        = count($forms);
            $progress     = count($doneSlugs);

            $statuses = [];
            foreach ($forms as $slug => $form) {
                $statuses[$slug] = in_array($slug, $doneSlugs);
            }

            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            return view('checklists.index', compact('inspection', 'forms', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'));
        }
        // ── Legacy system (inspections created before snapshot migration) ────

        if ($inspection->type_inspection === 'Facility Inspection') {
            $facilityForms  = self::getFacilityForms($inspection);
            $firstForm      = reset($facilityForms);
            $modelClass     = $firstForm['model'];
            $facilityRecord = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone   = $facilityRecord ? (array)($facilityRecord->sections_done ?? []) : [];
            $total          = count($facilityForms);
            $progress       = count($sectionsDone);

            // Finding counts per section
            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            $statuses     = [];
            $conformities = [];
            foreach ($facilityForms as $slug => $form) {
                $statuses[$slug]     = in_array($form['section'], $sectionsDone);
                $conformities[$slug] = $facilityRecord ? $facilityRecord->{$form['section'] . '_is_conforming'} : null;
            }

            return view('checklists.index', compact('inspection', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'))
                ->with('forms', $facilityForms);
        }

        if ($inspection->type_inspection === 'Process Inspection') {
            $processForms   = self::processInspectionForms();
            $firstForm      = reset($processForms);
            $modelClass     = $firstForm['model'];
            $processRecord  = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone   = $processRecord ? (array)($processRecord->sections_done ?? []) : [];
            $total          = count($processForms);
            $progress       = count($sectionsDone);

            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            $statuses     = [];
            $conformities = [];
            foreach ($processForms as $slug => $form) {
                $statuses[$slug]     = in_array($form['section'], $sectionsDone);
                $conformities[$slug] = $processRecord ? $processRecord->{$form['section'] . '_is_conforming'} : null;
            }

            return view('checklists.index', compact('inspection', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'))
                ->with('forms', $processForms);
        }

        if (self::isAmendmentType($inspection->type_inspection)) {
            $form       = self::amendmentDeviationForm();
            $modelClass = $form['model'];
            $record     = $modelClass::where('inspection_id', $inspection_id)->first();
            $statuses      = ['amendment-deviation' => (bool) $record];
            $conformities  = ['amendment-deviation' => $record?->is_conforming];
            $forms         = ['amendment-deviation' => $form];
            return view('checklists.index', compact('inspection', 'forms', 'statuses', 'conformities'));
        }

        if ($inspection->type_inspection === 'Study Protocol Inspection') {
            $spForms    = self::studyProtocolForms();
            $firstForm  = reset($spForms);
            $modelClass = $firstForm['model'];
            $spRecord   = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $spRecord ? (array)($spRecord->sections_done ?? []) : [];
            $total      = count($spForms);
            $progress   = count($sectionsDone);

            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            $statuses     = [];
            $conformities = [];
            foreach ($spForms as $slug => $form) {
                $statuses[$slug]     = in_array($form['section'], $sectionsDone);
                $conformities[$slug] = $spRecord ? $spRecord->{$form['section'] . '_is_conforming'} : null;
            }

            return view('checklists.index', compact('inspection', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'))
                ->with('forms', $spForms);
        }

        if ($inspection->type_inspection === 'Study Report Inspection') {
            $srForms    = self::studyReportForms();
            $firstForm  = reset($srForms);
            $modelClass = $firstForm['model'];
            $srRecord   = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $srRecord ? (array)($srRecord->sections_done ?? []) : [];
            $total      = count($srForms);
            $progress   = count($sectionsDone);

            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            $statuses     = [];
            $conformities = [];
            foreach ($srForms as $slug => $form) {
                $statuses[$slug]     = in_array($form['section'], $sectionsDone);
                $conformities[$slug] = $srRecord ? $srRecord->{$form['section'] . '_is_conforming'} : null;
            }

            return view('checklists.index', compact('inspection', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'))
                ->with('forms', $srForms);
        }

        if ($inspection->type_inspection === 'Data Quality Inspection') {
            $dqForms    = self::dataQualityForms();
            $firstForm  = reset($dqForms);
            $modelClass = $firstForm['model'];
            $dqRecord   = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $dqRecord ? (array)($dqRecord->sections_done ?? []) : [];
            $total      = count($dqForms);
            $progress   = count($sectionsDone);

            $findingCounts = \App\Models\Pro_QaInspectionFinding::where('inspection_id', $inspection_id)
                ->whereNotNull('facility_section')
                ->selectRaw('facility_section, count(*) as cnt')
                ->groupBy('facility_section')
                ->pluck('cnt', 'facility_section')
                ->toArray();

            $statuses     = [];
            $conformities = [];
            foreach ($dqForms as $slug => $form) {
                $statuses[$slug]     = in_array($form['section'], $sectionsDone);
                $conformities[$slug] = $dqRecord ? $dqRecord->{$form['section'] . '_is_conforming'} : null;
            }

            return view('checklists.index', compact('inspection', 'statuses', 'conformities', 'progress', 'total', 'findingCounts'))
                ->with('forms', $dqForms);
        }

        $forms = self::forms();

        $statuses     = [];
        $conformities = [];
        foreach ($forms as $slug => $form) {
            $modelClass          = $form['model'];
            $record              = $modelClass::where('inspection_id', $inspection_id)->first();
            $statuses[$slug]     = (bool) $record;
            $conformities[$slug] = $record?->is_conforming;
        }

        return view('checklists.index', compact('inspection', 'forms', 'statuses', 'conformities'));
    }

    /**
     * Show : affiche le formulaire d'un checklist.
     */
    public function show(int $inspection_id, string $slug)
    {
        $inspection    = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);

        if ($inspection->date_scheduled && now()->toDateString() < $inspection->date_scheduled) {
            return redirect('/project/create?project_id=' . $inspection->project_id . '#step6')
                ->with('error', 'Cette inspection ne peut pas être remplie avant sa date prévue (' . \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') . ').');
        }

        // ── Snapshot-based system ────────────────────────────────────────────
        if (InspectionSnapshot::where('inspection_id', $inspection_id)->where('url_slug', $slug)->exists()) {
            $form = ChecklistSnapshotService::buildForm($inspection, $slug);
            abort_if(!$form, 404, 'Formulaire introuvable.');

            $sectionCode = $form['section_code'];
            $formType    = $form['form_type'];
            $fieldPrefix = ChecklistSnapshotService::fieldPrefix(
                InspectionSnapshot::where('inspection_id', $inspection_id)->where('url_slug', $slug)->value('template_code') ?? '',
                $sectionCode,
                $formType
            );

            [$record, $dqAnswers, $dqV1Answers, $dqV2Answers] = ChecklistSnapshotService::buildRecord(
                $inspection_id, $sectionCode, $formType, $fieldPrefix
            );

            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            $personnels     = \App\Models\Pro_Personnel::orderBy('nom')->get();
            $all_personnels = $personnels;

            // DQ-specific prefills
            $dqQaManagerId = \DB::table('pro_key_facility_personnels')
                ->where('staff_role', 'Quality Assurance')
                ->where('active', 1)
                ->value('personnel_id');
            $dqQaManager = $dqQaManagerId ? \App\Models\Pro_Personnel::find($dqQaManagerId) : null;
            $dqProject   = $inspection->project_id
                ? \App\Models\Pro_Project::with('studyDirector')->find($inspection->project_id)
                : null;

            return view('checklists.form', compact(
                'inspection', 'slug', 'form', 'record', 'fieldPrefix',
                'all_personnels', 'dqAnswers', 'dqV1Answers', 'dqV2Answers',
                'sectionFindings', 'personnels', 'dqQaManager', 'dqProject'
            ));
        }
        // ── Legacy system ────────────────────────────────────────────────────

        // Amendment / Deviation Inspection (single-form, no prefix)
        if ($slug === 'amendment-deviation' && self::isAmendmentType($inspection->type_inspection)) {
            $form    = self::amendmentDeviationForm();
            $record  = $form['model']::where('inspection_id', $inspection_id)->first();
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->orderBy('id')
                ->get();
            $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
            return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'sectionFindings', 'personnels'));
        }

        $facilityForms = self::getFacilityForms($inspection);
        if (isset($facilityForms[$slug])) {
            $form        = $facilityForms[$slug];
            $record      = $form['model']::where('inspection_id', $inspection_id)->first();
            $fieldPrefix = $form['section'] . '_';
            // Findings scoped to this section
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            // All personnels for the findings form
            $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
            return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'fieldPrefix', 'sectionFindings', 'personnels'));
        }

        // Process Inspection sections
        $processForms = self::processInspectionForms();
        if (isset($processForms[$slug])) {
            $form        = $processForms[$slug];
            $record      = $form['model']::where('inspection_id', $inspection_id)->first();
            $fieldPrefix = $form['section'] . '_';
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
            return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'fieldPrefix', 'sectionFindings', 'personnels'));
        }

        // Study Protocol Inspection sections
        $spForms = self::studyProtocolForms();
        if (isset($spForms[$slug])) {
            $form        = $spForms[$slug];
            $record      = $form['model']::where('inspection_id', $inspection_id)->first();
            $fieldPrefix = $form['section'] . '_';
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
            return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'fieldPrefix', 'sectionFindings', 'personnels'));
        }

        // Study Report Inspection sections
        $srForms = self::studyReportForms();
        if (isset($srForms[$slug])) {
            $form        = $srForms[$slug];
            $record      = $form['model']::where('inspection_id', $inspection_id)->first();
            $fieldPrefix = $form['section'] . '_';
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
            return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'fieldPrefix', 'sectionFindings', 'personnels'));
        }

        // Data Quality Inspection sections
        $dqForms = self::dataQualityForms();
        if (isset($dqForms[$slug])) {
            $form        = $dqForms[$slug];
            $record      = $form['model']::where('inspection_id', $inspection_id)->first();
            $fieldPrefix = $form['section'] . '_';
            $section     = $form['section'];
            $formType    = $form['form_type'];

            $dqAnswers   = [];
            $dqV1Answers = [];
            $dqV2Answers = [];
            if ($record) {
                if (in_array($formType, ['dq_standard', 'study_box'])) {
                    $dqAnswers = (array)($record->{$section . '_answers'} ?? []);
                } else {
                    $dqV1Answers = (array)($record->{$section . '_v1_answers'} ?? []);
                    $dqV2Answers = (array)($record->{$section . '_v2_answers'} ?? []);
                }
            }

            $all_personnels = \App\Models\Pro_Personnel::orderBy('prenom')->get();
            $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
                ->where('inspection_id', $inspection_id)
                ->where('facility_section', $slug)
                ->orderBy('id')
                ->get();
            $personnels = $all_personnels;

            // Load QA Manager for pre-filling header info in section A
            $dqQaManagerId = \DB::table('pro_key_facility_personnels')
                ->where('staff_role', 'Quality Assurance')
                ->where('active', 1)
                ->value('personnel_id');
            $dqQaManager = $dqQaManagerId ? \App\Models\Pro_Personnel::find($dqQaManagerId) : null;

            // Load project with studyDirector relation
            $dqProject = $inspection->project_id
                ? \App\Models\Pro_Project::with('studyDirector')->find($inspection->project_id)
                : null;

            return view('checklists.form', compact(
                'inspection', 'slug', 'form', 'record', 'fieldPrefix',
                'all_personnels', 'dqAnswers', 'dqV1Answers', 'dqV2Answers',
                'sectionFindings', 'personnels', 'dqQaManager', 'dqProject'
            ));
        }

        $forms = self::forms();

        abort_if(!isset($forms[$slug]), 404, 'Formulaire introuvable.');

        $inspection = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);

        // For Critical Phase Inspections, only the selected checklist form is accessible
        if ($inspection->type_inspection === 'Critical Phase Inspection'
            && $inspection->checklist_slug
            && $slug !== $inspection->checklist_slug) {
            abort(403, 'Ce formulaire n\'est pas sélectionné pour cette inspection.');
        }

        $form       = $forms[$slug];
        $modelClass = $form['model'];
        $record     = $modelClass::where('inspection_id', $inspection_id)->first();

        // Load findings and personnels for the findings panel (scoped to inspection, no section)
        $sectionFindings = \App\Models\Pro_QaInspectionFinding::with('assignedTo')
            ->where('inspection_id', $inspection_id)
            ->orderBy('id')
            ->get();
        $personnels = \App\Models\Pro_Personnel::orderBy('nom')->get();

        return view('checklists.form', compact('inspection', 'slug', 'form', 'record', 'sectionFindings', 'personnels'));
    }

    /**
     * Save : sauvegarde (ou met à jour) les réponses d'un formulaire.
     */
    public function save(Request $request, int $inspection_id, string $slug)
    {

        $inspection = Pro_QaInspection::findOrFail($inspection_id);

        if ($inspection->date_scheduled && now()->toDateString() < $inspection->date_scheduled) {
            return redirect('/project/create?project_id=' . $inspection->project_id . '#step6')
                ->with('error', 'Cette inspection ne peut pas être remplie avant sa date prévue (' . \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') . ').');
        }

        
        if ($inspection->completed_at) {
            return redirect()->back()
            ->with('error', 'This inspection has been marked as completed. The form can no longer be modified.');
            }

        // ── Snapshot-based system ────────────────────────────────────────────
        if (InspectionSnapshot::where('inspection_id', $inspection_id)->where('url_slug', $slug)->exists()) {
            ChecklistSnapshotService::saveSection($request, $inspection, $slug);

            // Auto-mark inspection as performed when all sections are saved
            if (!$inspection->date_performed) {
                $allSlugs  = InspectionSnapshot::where('inspection_id', $inspection_id)
                    ->pluck('url_slug')->unique()->values()->toArray();
                $doneSlugs = ChecklistSnapshotService::doneSlugs($inspection_id);
                if (!array_diff($allSlugs, $doneSlugs)) {
                    $inspection->date_performed = now()->toDateString();
                    $inspection->save();
                        $this->notifyInspectionComplete($inspection);
                }
            }

            $form = ChecklistSnapshotService::buildForm($inspection, $slug);
            $label = $form ? (($form['letter'] ? $form['letter'] . '. ' : '') . $form['title']) : $slug;

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . $label . '" enregistrée avec succès.');
        }
        // ── Legacy system ────────────────────────────────────────────────────

        // Amendment / Deviation Inspection
        if ($slug === 'amendment-deviation' && self::isAmendmentType($inspection->type_inspection)) {
            $form       = self::amendmentDeviationForm();
            $modelClass = $form['model'];

            $data = [
                'inspection_id'    => $inspection_id,
                'filled_by'        => auth()->id() ?? null,
                'document_type'    => $request->input('document_type'),
                'deviation_number' => $request->input('deviation_number'),
                'amendment_number' => $request->input('amendment_number'),
                'comments'         => $request->input('comments'),
            ];
            foreach (array_keys($form['questions']) as $n) {
                $data["q{$n}"] = $request->input("q{$n}");
            }
            $data['is_conforming'] = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);


            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            if (!$inspection->date_performed) {
                $inspection->date_performed = now()->toDateString();
                $inspection->save();
                    $this->notifyInspectionComplete($inspection);
            }

            return redirect('/project/create?project_id=' . $inspection->project_id . '#step6')
                ->with('success', 'Amendment/Deviation checklist enregistré avec succès.');
        }

        $facilityForms = self::getFacilityForms($inspection);
        if (isset($facilityForms[$slug])) {
            $form       = $facilityForms[$slug];
            $modelClass = $form['model'];
            $section    = $form['section'];
            $prefix     = $section . '_';

            $data = [
                'project_id'    => $inspection->project_id,
                'project_code'  => $inspection->project?->project_code,
                'inspection_id' => $inspection_id,
                'filled_by'     => auth()->id() ?? null,
            ];

            foreach (array_keys($form['questions']) as $n) {
                $data["{$prefix}q{$n}"] = $request->input("{$prefix}q{$n}");
            }
            $data["{$prefix}comments"]      = $request->input("{$prefix}comments");
            $data["{$prefix}is_conforming"] = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);

            // Append section to sections_done if not already present
            $existing     = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $existing ? (array)($existing->sections_done ?? []) : [];
            if (!in_array($section, $sectionsDone)) {
                $sectionsDone[] = $section;
            }
            $data['sections_done'] = $sectionsDone;

            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . strtoupper($section) . '. ' . $form['title'] . '" enregistrée avec succès.');
        }

        // Process Inspection sections
        $processForms = self::processInspectionForms();
        if (isset($processForms[$slug])) {
            $form       = $processForms[$slug];
            $modelClass = $form['model'];
            $section    = $form['section'];
            $prefix     = $section . '_';

            $data = [
                'inspection_id' => $inspection_id,
                'filled_by'     => auth()->id() ?? null,
            ];

            foreach (array_keys($form['questions']) as $n) {
                $data["{$prefix}q{$n}"] = $request->input("{$prefix}q{$n}");
            }
            $data["{$prefix}comments"]      = $request->input("{$prefix}comments");
            $data["{$prefix}is_conforming"] = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);

            // Append section to sections_done if not already present
            $existing     = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $existing ? (array)($existing->sections_done ?? []) : [];
            if (!in_array($section, $sectionsDone)) {
                $sectionsDone[] = $section;
            }
            $data['sections_done'] = $sectionsDone;

            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . strtoupper($section) . '. ' . $form['title'] . '" enregistrée avec succès.');
        }

        // Study Protocol Inspection sections
        $spForms = self::studyProtocolForms();
        if (isset($spForms[$slug])) {
            $form       = $spForms[$slug];
            $modelClass = $form['model'];
            $section    = $form['section'];
            $prefix     = $section . '_';

            $data = [
                'inspection_id' => $inspection_id,
                'filled_by'     => auth()->id() ?? null,
            ];

            foreach (array_keys($form['questions']) as $n) {
                $data["{$prefix}q{$n}"] = $request->input("{$prefix}q{$n}");
            }
            $data["{$prefix}comments"] = $request->input("{$prefix}comments");

            // Conclusion : conformité pour toutes les sections du Study Protocol
            $data["{$prefix}is_conforming"] = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);

            // Section F special: staff training records — iterate over submitted keys only
            if (($form['type'] ?? '') === 'study_personnel') {
                foreach ($request->all() as $key => $value) {
                    if (preg_match('/^f_staff_(\d+)_(result|level|remarks)$/', $key)) {
                        $data[$key] = $value;
                    }
                }
            }

            // Append section to sections_done if not already present
            $existing     = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $existing ? (array)($existing->sections_done ?? []) : [];
            if (!in_array($section, $sectionsDone)) {
                $sectionsDone[] = $section;
            }
            $data['sections_done'] = $sectionsDone;

            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            // Auto-finaliser quand toutes les sections A–F sont complétées
            $allSpSections = ['a','b','c','d','e','f'];
            if (!$inspection->date_performed && !array_diff($allSpSections, $sectionsDone)) {
                $inspection->date_performed = now()->toDateString();
                $inspection->save();
                    $this->notifyInspectionComplete($inspection);
            }

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . strtoupper($section) . '. ' . $form['title'] . '" enregistrée avec succès.');
        }

        // Study Report Inspection sections
        $srForms = self::studyReportForms();
        if (isset($srForms[$slug])) {
            $form       = $srForms[$slug];
            $modelClass = $form['model'];
            $section    = $form['section'];
            $prefix     = $section . '_';

            $data = [
                'inspection_id' => $inspection_id,
                'filled_by'     => auth()->id() ?? null,
            ];

            foreach (array_keys($form['questions']) as $n) {
                $data["{$prefix}q{$n}"] = $request->input("{$prefix}q{$n}");
            }
            $data["{$prefix}comments"] = $request->input("{$prefix}comments");

            // Conformity per section
            $data["{$prefix}is_conforming"] = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);

            // Append section to sections_done if not already present
            $existing     = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $existing ? (array)($existing->sections_done ?? []) : [];
            if (!in_array($section, $sectionsDone)) {
                $sectionsDone[] = $section;
            }
            $data['sections_done'] = $sectionsDone;

            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            // Auto-finaliser quand toutes les sections A–E sont complétées
            $allSrSections = ['a','b','c','d','e'];
            if (!$inspection->date_performed && !array_diff($allSrSections, $sectionsDone)) {
                $inspection->date_performed = now()->toDateString();
                $inspection->save();
                    $this->notifyInspectionComplete($inspection);
            }

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . strtoupper($section) . '. ' . $form['title'] . '" enregistrée avec succès.');
        }

        // Data Quality Inspection sections
        $dqForms = self::dataQualityForms();
        if (isset($dqForms[$slug])) {
            $form     = $dqForms[$slug];
            $modelClass = $form['model'];
            $section  = $form['section'];
            $formType = $form['form_type'];

            $data = ['inspection_id' => $inspection_id];

            if ($formType === 'dq_standard') {
                $answers = [];
                foreach (array_keys($form['questions']) as $n) {
                    $answers[(string)$n] = $request->input("q_{$n}");
                }
                $data["{$section}_answers"]          = $answers;
                $data["{$section}_date_performed"]   = $request->input("{$section}_date_performed") ?: null;
                $data["{$section}_qa_personnel_id"]  = $request->input("{$section}_qa_personnel_id") ?: null;
                $data["{$section}_comments"]         = $request->input("{$section}_comments");
                $data["{$section}_is_conforming"]    = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);

                // Header info only for section A
                if ($section === 'a') {
                    $data['aspects_inspected']   = $request->input('aspects_inspected', []);
                    $data['study_start_date']    = $request->input('study_start_date') ?: null;
                    $data['study_end_date']      = $request->input('study_end_date') ?: null;
                    $data['study_director_name'] = $request->input('study_director_name');
                    $data['qa_inspector_phone']  = $request->input('qa_inspector_phone');
                    $data['qa_inspector_email']  = $request->input('qa_inspector_email');
                    $data['personnel_involved']  = $request->input('personnel_involved', []);
                }
            } elseif ($formType === 'dual_verification') {
                $v1 = [];
                $v2 = [];
                foreach (array_keys($form['questions']) as $n) {
                    $v1[(string)$n] = $request->input("v1_q_{$n}");
                    $v2[(string)$n] = $request->input("v2_q_{$n}");
                }
                $data["{$section}_v1_answers"]          = $v1;
                $data["{$section}_v1_date_performed"]   = $request->input("{$section}_v1_date_performed") ?: null;
                $data["{$section}_v1_qa_personnel_id"]  = $request->input("{$section}_v1_qa_personnel_id") ?: null;
                $data["{$section}_v2_answers"]          = $v2;
                $data["{$section}_v2_date_performed"]   = $request->input("{$section}_v2_date_performed") ?: null;
                $data["{$section}_v2_qa_personnel_id"]  = $request->input("{$section}_v2_qa_personnel_id") ?: null;
                $data["{$section}_comments"]            = $request->input("{$section}_comments");
                $data["{$section}_is_conforming"]       = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);
            } elseif ($formType === 'study_box') {
                $answers = [];
                foreach (array_keys($form['questions']) as $n) {
                    $answers[(string)$n] = [
                        'response' => $request->input("q_{$n}_response"),
                        'signed'   => $request->input("q_{$n}_signed"),
                    ];
                }
                $data["{$section}_answers"]         = $answers;
                $data["{$section}_date_performed"]  = $request->input("{$section}_date_performed") ?: null;
                $data["{$section}_qa_personnel_id"] = $request->input("{$section}_qa_personnel_id") ?: null;
                $data["{$section}_comments"]        = $request->input("{$section}_comments");
                $data["{$section}_is_conforming"]   = $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null);
            }

            // Append section to sections_done
            $existing     = $modelClass::where('inspection_id', $inspection_id)->first();
            $sectionsDone = $existing ? (array)($existing->sections_done ?? []) : [];
            if (!in_array($section, $sectionsDone)) {
                $sectionsDone[] = $section;
            }
            $data['sections_done'] = $sectionsDone;

            $modelClass::updateOrCreate(['inspection_id' => $inspection_id], $data);

            // Auto-finaliser quand toutes les sections A–E sont complétées
            $allDqSections = ['a', 'b', 'c', 'd', 'e'];
            if (!$inspection->date_performed && !array_diff($allDqSections, $sectionsDone)) {
                $inspection->date_performed = now()->toDateString();
                $inspection->save();
                    $this->notifyInspectionComplete($inspection);
            }

            return redirect()->route('checklist.index', $inspection_id)
                ->with('success', 'Section "' . strtoupper($section) . '. ' . $form['title'] . '" enregistrée avec succès.');
        }

        $forms = self::forms();
        abort_if(!isset($forms[$slug]), 404, 'Formulaire introuvable.');

        // Block saving non-selected forms for Critical Phase Inspections
        if ($inspection->type_inspection === 'Critical Phase Inspection'
            && $inspection->checklist_slug
            && $slug !== $inspection->checklist_slug) {
            abort(403);
        }

        $form       = $forms[$slug];
        $modelClass = $form['model'];
        $questions  = $form['questions'];

        // Construire le tableau des réponses
        $data = [
            'project_id'   => $inspection->project_id,
            'project_code' => $inspection->project?->project_code,
            'inspection_id'=> $inspection_id,
            'comments'     => $request->input('comments'),
            'is_conforming' => $request->input('is_conforming') === '1' ? true : ($request->input('is_conforming') === '0' ? false : null),
            'filled_by'    => auth()->id() ?? null,
        ];

        foreach (array_keys($questions) as $n) {
            $data['q' . $n] = $request->input('q' . $n);
        }

        $modelClass::updateOrCreate(
            ['inspection_id' => $inspection_id],
            $data
        );

        // Auto-mark the Critical Phase Inspection as done when its checklist is saved
        if ($inspection->type_inspection === 'Critical Phase Inspection' && !$inspection->date_performed) {
            $inspection->date_performed = now()->toDateString();
            $inspection->save();
                $this->notifyInspectionComplete($inspection);
        }

        $redirectUrl = '/project/create?project_id=' . $inspection->project_id . '#step6';

        return redirect($redirectUrl)
            ->with('success', 'Checklist "' . $form['letter'] . '. ' . $form['title'] . '" enregistré avec succès.');
    }

    /**
     * Charge les personnels clés par rôle (QA Manager, Facility Manager…)
     * Retourne un tableau indexé par staff_role en minuscules sans espaces.
     */
    private static function keyPersonnels(): array
    {
        $rows = Pro_KeyFacilityPersonnel::where('active', 1)->get();
        $result = [];
        foreach ($rows as $row) {
            $person = Pro_Personnel::find($row->personnel_id);
            if ($person) {
                $key = strtolower(str_replace(' ', '_', $row->staff_role));
                $result[$key] = $person;
            }
        }
        return $result;
    }

    /**
     * Facility Print : imprime le checklist complet (toutes sections) au format PDF-like.
     * ?mode=empty pour formulaire vierge, ?mode=filled (défaut) pour formulaire rempli.
     */
    public function facilityPrint(int $inspection_id, Request $request)
    {
        $inspection   = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if($inspection->type_inspection !== 'Facility Inspection', 404);

        $facilityForms = self::getFacilityForms($inspection);
        $firstForm     = reset($facilityForms);
        $modelClass    = $firstForm['model'];
        $record        = $modelClass::where('inspection_id', $inspection_id)->first();

        $mode     = $request->query('mode', 'filled');
        $location = $inspection->facility_location ?? 'cotonou';

        // Key personnels
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.facility-print', compact(
            'inspection', 'facilityForms', 'record', 'mode', 'location', 'keyPersonnels'
        ));
    }

    /**
     * Process Inspection print view.
     * ?mode=empty pour formulaire vierge, ?mode=filled (défaut) pour formulaire rempli.
     */
    public function processPrint(int $inspection_id, Request $request)
    {
        $inspection  = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if($inspection->type_inspection !== 'Process Inspection', 404);

        $processForms = self::processInspectionForms();
        $record       = \App\Models\Pro_Cl_ProcessInspection::where('inspection_id', $inspection_id)->first();
        $mode         = $request->query('mode', 'filled');
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.process-print', compact(
            'inspection', 'processForms', 'record', 'mode', 'keyPersonnels'
        ));
    }

    /**
     * Study Protocol Inspection print view.
     * ?mode=empty pour formulaire vierge, ?mode=filled (défaut) pour formulaire rempli.
     */
    public function studyProtocolPrint(int $inspection_id, Request $request)
    {
        $inspection  = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if($inspection->type_inspection !== 'Study Protocol Inspection', 404);

        $spForms = self::studyProtocolForms();
        $record  = \App\Models\Pro_Cl_StudyProtocolInspection::where('inspection_id', $inspection_id)->first();
        $mode    = $request->query('mode', 'filled');
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.study-protocol-print', compact(
            'inspection', 'spForms', 'record', 'mode', 'keyPersonnels'
        ));
    }

    /**
     * Study Report Inspection print view.
     * ?mode=empty pour formulaire vierge, ?mode=filled (défaut) pour formulaire rempli.
     */
    public function studyReportPrint(int $inspection_id, Request $request)
    {
        $inspection  = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if($inspection->type_inspection !== 'Study Report Inspection', 404);

        $srForms = self::studyReportForms();
        $record  = \App\Models\Pro_Cl_StudyReportInspection::where('inspection_id', $inspection_id)->first();
        $mode    = $request->query('mode', 'filled');
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.study-report-print', compact(
            'inspection', 'srForms', 'record', 'mode', 'keyPersonnels'
        ));
    }

    /**
     * Data Quality Inspection print view.
     */
    public function dataQualityPrint(int $inspection_id, Request $request)
    {
        $inspection = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if($inspection->type_inspection !== 'Data Quality Inspection', 404);

        $dqForms = self::dataQualityForms();
        $record  = \App\Models\Pro_Cl_DataQualityInspection::where('inspection_id', $inspection_id)->first();
        $mode    = $request->query('mode', 'filled');
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.data-quality-print', compact(
            'inspection', 'dqForms', 'record', 'mode', 'keyPersonnels'
        ));
    }

    /**
     * Amendment/Deviation Inspection print view.
     * ?mode=empty pour formulaire vierge, ?mode=filled (défaut) pour formulaire rempli.
     */
    public function amendmentPrint(int $inspection_id, Request $request)
    {
        $inspection  = Pro_QaInspection::with('inspector', 'project')->findOrFail($inspection_id);
        abort_if(!self::isAmendmentType($inspection->type_inspection), 404);

        $form   = self::amendmentDeviationForm();
        $record = $form['model']::where('inspection_id', $inspection_id)->first();
        $mode   = $request->query('mode', 'filled');
        $keyPersonnels = self::keyPersonnels();

        return view('checklists.amendment-print', compact(
            'inspection', 'form', 'record', 'mode', 'keyPersonnels'
        ));
    }

    /**
     * Report : génère le rapport QA Unit Report pour une inspection.
     */
    public function report(int $inspection_id)
    {
        $inspection = Pro_QaInspection::with([
            'inspector',
            'project.studyDirector',
            'findings.assignedTo',
        ])->findOrFail($inspection_id);

        $forms        = self::forms();
        $keyPersonnels = self::keyPersonnels();

        // Section metadata for grouping findings (Facility/Process/StudyProtocol only)
        $sectionsMeta = [];
        if ($inspection->type_inspection === 'Facility Inspection') {
            $facilityForms = self::getFacilityForms($inspection);
            foreach ($facilityForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Process Inspection') {
            $processForms = self::processInspectionForms();
            foreach ($processForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Study Protocol Inspection') {
            $spForms = self::studyProtocolForms();
            foreach ($spForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Study Report Inspection') {
            $srForms = self::studyReportForms();
            foreach ($srForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Data Quality Inspection') {
            $dqForms = self::dataQualityForms();
            foreach ($dqForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        }

        return view('checklists.report', compact('inspection', 'forms', 'keyPersonnels', 'sectionsMeta'));
    }

    /**
     * Follow-up report : QA Findings Response (Follow-Up) pour une inspection.
     */
    public function followup(int $inspection_id)
    {
        $inspection = Pro_QaInspection::with([
            'inspector',
            'project.studyDirector',
            'findings.assignedTo',
        ])->findOrFail($inspection_id);

        $keyPersonnels = self::keyPersonnels();

        // Section metadata for grouping findings (Facility/Process/StudyProtocol only)
        $sectionsMeta = [];
        if ($inspection->type_inspection === 'Facility Inspection') {
            $facilityForms = self::getFacilityForms($inspection);
            foreach ($facilityForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Process Inspection') {
            $processForms = self::processInspectionForms();
            foreach ($processForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Study Protocol Inspection') {
            $spForms = self::studyProtocolForms();
            foreach ($spForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        } elseif ($inspection->type_inspection === 'Study Report Inspection') {
            $srForms = self::studyReportForms();
            foreach ($srForms as $slug => $form) {
                $sectionsMeta[$slug] = $form['letter'] . '. ' . $form['title'];
            }
        }

        return view('checklists.followup', compact('inspection', 'keyPersonnels', 'sectionsMeta'));
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

        // Facility sections: detect location from inspection to pick the right forms
        $inspection     = Pro_QaInspection::find($inspection_id);
        $facilityForms  = $inspection ? self::getFacilityForms($inspection) : self::facilityForms();
        $firstForm      = reset($facilityForms);
        $modelClass     = $firstForm['model'];
        $facilityRecord = $modelClass::where('inspection_id', $inspection_id)->first();
        $sectionsDone   = $facilityRecord ? (array)($facilityRecord->sections_done ?? []) : [];

        foreach ($facilityForms as $slug => $form) {
            $statuses[$slug] = in_array($form['section'], $sectionsDone);
        }
        $statuses['facility_progress'] = count($sectionsDone);

        // Process Inspection sections
        $processForms   = self::processInspectionForms();
        $processRecord  = \App\Models\Pro_Cl_ProcessInspection::where('inspection_id', $inspection_id)->first();
        $processDone    = $processRecord ? (array)($processRecord->sections_done ?? []) : [];

        foreach ($processForms as $slug => $form) {
            $statuses[$slug] = in_array($form['section'], $processDone);
        }
        $statuses['process_progress'] = count($processDone);

        return response()->json([
            'success'  => true,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Registre des 15 sections du Facility Inspection Checklist — Cotonou.
     */
    private static function facilityForms(): array
    {
        $allSections = self::facilityForm()['sections'];
        $result = [];

        foreach (array_keys($allSections) as $sec) {
            $result["facility-{$sec}"] = [
                'model'    => \App\Models\Pro_Cl_FacilityInspection::class,
                'section'  => $sec,
                'letter'   => strtoupper($sec),
                'title'    => $allSections[$sec]['title'],
                'questions' => $allSections[$sec]['questions'],
                'location' => 'cotonou',
            ];
        }

        return $result;
    }

    /**
     * Registre des 9 sections du Facility Inspection Checklist — Covè.
     */
    private static function facilityFormsCove(): array
    {
        $allSections = self::facilityFormCove()['sections'];
        $result = [];

        foreach (array_keys($allSections) as $sec) {
            $result["facility-cove-{$sec}"] = [
                'model'    => \App\Models\Pro_Cl_FacilityInspectionCove::class,
                'section'  => $sec,
                'letter'   => strtoupper($sec),
                'title'    => $allSections[$sec]['title'],
                'questions' => $allSections[$sec]['questions'],
                'location' => 'cove',
            ];
        }

        return $result;
    }

    /**
     * Returns the appropriate facility forms based on inspection location.
     */
    private static function getFacilityForms(\App\Models\Pro_QaInspection $inspection): array
    {
        return $inspection->facility_location === 'cove'
            ? self::facilityFormsCove()
            : self::facilityForms();
    }

    /**
     * Process Inspection forms registry (5 sections A-E).
     */
    private static function processInspectionForms(): array
    {
        $allSections = self::processInspectionForm()['sections'];
        $result = [];
        foreach (array_keys($allSections) as $sec) {
            $result["process-{$sec}"] = [
                'model'     => \App\Models\Pro_Cl_ProcessInspection::class,
                'section'   => $sec,
                'letter'    => strtoupper($sec),
                'title'     => $allSections[$sec]['title'],
                'questions' => $allSections[$sec]['questions'],
            ];
        }
        return $result;
    }

    private static function processInspectionForm(): array
    {
        return [
            'sections' => [
                'a' => [
                    'title' => 'Equipment Reception, Installation and Management',
                    'questions' => [
                        1  => 'Is there a designated personnel for the management of CREC/LSHTM equipment?',
                        2  => 'Is a request made for the purchase of each type of equipment?',
                        3  => 'At delivery, are equipment in conformity with requirements submitted before purchase?',
                        4  => 'Are equipment calibrated by an external body before first use and annually or according to agreed calibration frequency?',
                        5  => 'Are calibration certificates attached at delivery or before First use?',
                        6  => 'Are equipment delivered with manufacturer\'s guide?',
                        7  => 'Are equipment approved by the Facility Manager?',
                        8  => 'Is an internal code attributed to equipment and equipment included in the inventory list? (Registration)',
                        9  => 'Are equipment tested after installation?',
                        10 => 'Is a file created for each type of equipment?',
                        11 => 'Are equipment stored according to the manufacturer\'s guide?',
                        12 => 'Are there SOPs written and available for type of equipment and in every section?',
                        13 => 'Is an equipment inventory list maintained and up to date for the equipment?',
                        14 => 'Are equipment use to calibrate other equipment (e.g. Master data logger) indicated on the equipment inventory list as \'\'Reference\'\'?',
                        15 => 'Are staff trained on the use of equipment?',
                        16 => 'Are equipment used for intended purpose?',
                        17 => 'Are all equipment forms filled regularly and accordingly?',
                        18 => 'Is maintenance done on each equipment and according to the equipment maintenance programme?',
                        19 => 'Are Maintenance logs on equipment up-to-date?',
                        20 => 'Is equipment history regularly written in the log book?',
                        21 => 'Are all calibration certificates up to date?',
                        22 => 'Are internal calibrations performed regularly by staff and according to the established internal calibration programme?',
                        23 => 'Are GLP equipment identified as such?',
                        24 => 'Are faulty or obsolete equipment taken out of the system and labelled \'\'Do not use\'\'?',
                    ],
                ],
                'b' => [
                    'title' => 'Test Item Reception, Storage and Management',
                    'questions' => [
                        1  => 'Is there someone responsible for the reception, storage and management of CREC/LSHTM test items?',
                        2  => 'Is there a procedure for the reception, storage and management of test items?',
                        3  => 'Is there an SOP for the reception, registration and storage of test items?',
                        4  => 'Is the reception of test/control/reference items documented?',
                        5  => 'Were the documents such as certificates of analysis, MSDS, Correspondence etc. attached to test item upon delivery?',
                        6  => 'If documents are not attached to test item upon delivery, is there a procedure in place to ensure they are requested from supplier or sponsor and made available to the personnel in charge of the managing CREC/LSHTM test items?',
                        7  => 'Are all test items related documents kept in a folder for reference purpose?',
                        8  => 'Are test items registered at reception (i.e. Test Item reception form filled and signed)',
                        9  => 'Is an internal code (CREC Chemical code) attributed to each test item during registration?',
                        10 => 'Is an acknowledgment of receipt filled and signed by the SD?',
                        11 => 'Is reception feedback done to supplier or sponsor (Acknowledgement of receipt sent?)',
                        12 => 'Are Test/control/reference substances properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions, MSDS) during storage?',
                        13 => 'Is the test item stored according to requirements stated at delivery?',
                        14 => 'Are test item dilutions labelled to ensure proper identification of test item?',
                        15 => 'Is the environmental condition of storage area monitored and recorded on daily basis?',
                        16 => 'Are there records of test/control/reference items usage?',
                        17 => 'Are there records of test items transport conditions?',
                        18 => 'Is there a procedure for disposal of expired test items?',
                        19 => 'Is test item disposal documented?',
                        20 => 'Is access to CREC/LSHTM Test items limited?',
                    ],
                ],
                'c' => [
                    'title' => 'Test System Request, Production, Supply and Management',
                    'questions' => [
                        1  => 'Is there a designated person in charge of test system request, production, supply and management?',
                        2  => 'Is there a procedure in place for the request, production, supply and management of CREC/LSHTM Test system?',
                        3  => 'Is there an SOP for the request, production, supply and management of test system?',
                        4  => 'Is the production of test system done on a daily basis and are records of production kept?',
                        5  => 'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                        6  => 'Are breeding cages separated from test cages?',
                        7  => 'Are adult mosquitoes separated from larvae?',
                        8  => 'Is the environmental condition of rearing areas being monitored and recorded?',
                        9  => 'When environmental conditions are out of required range, is this reported using a minor or a major incident form?',
                        10 => 'Is resistance test performed for each mosquito strain?',
                        11 => 'Is the resistance status report provided to the FM and satisfactory?',
                        12 => 'When test system is supplied to the insectary, is the reception date recorded?',
                        13 => 'Is the source of the test system supplied stated on the mosquito reception sheet?',
                        14 => 'Upon reception of test system at the insectary are information such as Date received, Species, Strain, Stage, Estimated Quantity and Code recorded?',
                        15 => 'When test system is needed by other units of CREC/LSHTM facility, is a test system request submitted to the insectary supervisor for supply of mosquitoes?',
                        16 => 'Are mosquitoes supplied as requested or within an acceptable period?',
                        17 => 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to.',
                        18 => 'Are the material transfer sheets and chain of custody sheets filled in during operation between the insectary and other units?',
                        19 => 'Are mosquitoes being controlled during transportation and are there records of this?',
                        20 => 'Are all test item related sheets signed by the unit supervisor?',
                        21 => 'Are breeding animal separated from test animal?',
                        22 => 'Are SOPs related to animal house activities available?',
                        23 => 'Are animals identified using an internal ID code, name, sex, date of birth, colour of fur, size, and physiological status?',
                        24 => 'Are animal maintained on a daily basis and are records of maintenance activities kept?',
                        25 => 'Are animal maintenance forms regularly filled and signed?',
                        26 => 'Is there a documented procedure for releasing animals for test or blood feeding?',
                        27 => 'When animals are needed for testing, is an animal request form submitted to staff in charge for supply of animals?',
                        28 => 'Are animals released as requested?',
                        29 => 'Is an animal release form filled during release of animals?',
                        30 => 'In the case where animals are returned to the animal house, are there records of this?',
                        31 => 'Are animals followed up individually and is the individual animal follow-up filled in and signed?',
                    ],
                ],
                'd' => [
                    'title' => 'Computerized system Reception, registration, validation and maintenance',
                    'questions' => [
                        1  => 'Is there a designated person in charge of the CREC/LSHTM computerized system?',
                        2  => 'Is a user request form filled before the purchase of computerized system?',
                        3  => 'Are purchase approved by the FM?',
                        4  => 'Upon delivery, are documents such as computer\'s Manual, Guarantee and Characteristics form etc. attached to computerised system?',
                        5  => 'In the case where these documents are not attached upon delivery, is there a system in place to ensure they are made available to the personnel in charge of computerized system?',
                        6  => 'Is the user acceptance form filled and signed during reception?',
                        7  => 'Is a risk assessment performed during reception?',
                        8  => 'Is the computerized system validated and registered during reception?',
                        9  => 'Is the computerised system configured after reception?',
                        10 => 'Is there a folder for each computerized system?',
                        11 => 'Are GLP computerized system clearly identified and separated from non-GLP?',
                        12 => 'Is there a programme for the validation of softwares of computerized systems?',
                        13 => 'Are validations performed according to established programme and are records of these kept?',
                        14 => 'Are validations approved by the FM?',
                        15 => 'Is there a programme for the maintenance of computerized system?',
                        16 => 'Is maintenance performed following the established programme and is this recorded?',
                        17 => 'Is the server regularly maintained?',
                        18 => 'Are activities performed on computerized system recorded in the computer logbook?',
                        19 => 'Are computers secured with a password system?',
                        20 => 'Are data sent from other units verified to ensure they were not corrupted during transfer?',
                        21 => 'Is the data verification checklist regularly filled and signed?',
                        22 => 'Is there a backup system in place where data are secured?',
                        23 => 'Is there an inventory list of CREC/LSHTM computerized system and is the list up to date?',
                        24 => 'Is there a procedure for the retrieval of data?',
                        25 => 'Is there a procedure for the retrieval/disposal of computerized system?',
                    ],
                ],
                'e' => [
                    'title' => 'Safety Procedures',
                    'questions' => [
                        1  => 'Is there a designated person in charge of the Safety?',
                        2  => 'Is there a calendar for safety inspections?',
                        3  => 'Is risk assessment performed before each Glp studies?',
                        4  => 'Are PPE clothing put on during laboratory activities?',
                        5  => 'Is decontamination regularly done before using any materials?',
                        6  => 'Are fire extinguishers accessible and up to date yearly?',
                        7  => 'Are first aid kits regularly checked and safety material to ensure they are intact?',
                        8  => 'Are incidents reported effectively and submitted to safety officer?',
                        9  => 'Are corrective actions implemented after incident reports?',
                        10 => 'Are work related health issues reported in cases of occurrences?',
                        11 => 'Is the Respirator Qualitative Face Fit test performed for each staff each year?',
                    ],
                ],
            ],
        ];
    }

    /**
     * Définition du formulaire Facility Inspection Checklist — Covè (QA-PR-1-001B/06).
     * 9 sections (A–I).
     */
    private static function facilityFormCove(): array
    {
        return [
            'model'   => \App\Models\Pro_Cl_FacilityInspectionCove::class,
            'letter'  => 'FI-C',
            'title'   => 'Facility Inspection Checklist (Field Site — Covè)',
            'doc_ref' => 'QA-PR-1-001B/06',
            'sections' => [
                'a' => [
                    'title' => 'General',
                    'questions' => [
                        1 => 'Have all non-conformances raised from previous facility inspection been corrected?',
                    ],
                ],
                'b' => [
                    'title' => 'Staff Offices & Buildings',
                    'questions' => [
                        1  => 'Are different sections within the Facility clearly defined?',
                        2  => 'Are all entry ways secured from unauthorized access?',
                        3  => 'Are offices clean and well maintained?',
                        4  => 'Is the entire building clean and well maintained on daily basis?',
                        5  => 'Are all floors free of liquids to avoid trips and falls?',
                        6  => 'Is there any housekeeping issues that need to be addressed?',
                        7  => 'Are all plugs and cords in good condition?',
                        8  => 'Are there electrical switches, switch plates or receptacles that are cracked, broken or have exposed contacts?',
                        9  => 'Are all electrical circuit breakers identified?',
                        10 => 'Is there any circuit breakers regularly tripping?',
                        11 => 'Are surveillance video cameras working?',
                        12 => 'Is the building equipped with fire extinguishers?',
                        13 => 'Are there any security issues to be addressed?',
                        14 => 'Is the organizational chart up to date and available in every section?',
                        15 => 'Is a floor plan up to date and available?',
                        16 => 'Is there a copy of the field site staff file?',
                        17 => 'Are SOPs related to field site activities up to date and available?',
                        18 => 'Are all data entry computers protected by a code system?',
                        19 => 'Are computers regularly maintained?',
                        20 => 'Are all computers equipped with an up-to-date anti-virus programme?',
                        21 => 'Is there a backup system in place where data are secured?',
                        22 => 'Is data recorded directly, legibly and indelibly?',
                        23 => 'Are all data signed and dated at the time of entry?',
                        24 => 'Are softwares regularly validated?',
                    ],
                ],
                'c' => [
                    'title' => 'Bioassay Laboratory Field site',
                    'questions' => [
                        1  => 'Is the bioassay laboratory secured from unauthorised access?',
                        2  => 'Is the work area neat?',
                        3  => 'Is the water under the tables clean and changed regularly?',
                        4  => 'Are laboratory tools safely secured and stored when not in used?',
                        5  => 'Are racks labelled accordingly providing detailed information and well arranged?',
                        6  => 'Are appropriate dress procedures followed?',
                        7  => 'Are insecticide product waste disposed separately from regular waste?',
                        8  => 'Is the laboratory environmentally controlled?',
                        9  => 'Is the laboratory temperature monitored?',
                        10 => 'Are lab coats clean and well arranged?',
                        11 => 'Is there a person designated as responsible for equipment, is this clearly defined and are designated individuals aware of their responsibilities?',
                        12 => 'Are equipment cleaned after use?',
                        13 => 'Are equipment uniquely identified and included on the equipment inventory list?',
                        14 => 'Do Equipment appear to be in good repair?',
                        15 => 'Are Equipment adequately stored when not used?',
                        16 => 'Are Instructions manual easily accessible?',
                        17 => 'Are Equipment SOP easily accessible and available and each piece of equipment?',
                        18 => 'Are Usage/maintenance/calibration/fault report sheets accessible and regularly filled in?',
                        19 => 'Is the Equipment calibration programme defined and regularly followed?',
                        20 => 'Are Maintenance logs on equipment up-to-date?',
                        21 => 'Are Equipment history regularly written in the log book?',
                        22 => 'Are SOPs related to laboratory activities up to date, signed and available?',
                    ],
                ],
                'd' => [
                    'title' => 'Chemical Room & Non-treated material Room',
                    'questions' => [
                        1  => 'Is there someone responsible for the management of the chemical room?',
                        2  => 'Is there limited access to chemical storage room and non-treated material storage room?',
                        3  => 'Is the Test/control/reference substance storage room neat and organized?',
                        4  => 'Are there separate areas for storage of test/control/reference items?',
                        5  => 'Is the chemical storage room environmentally controlled?',
                        6  => 'Is the chemical room temperature continuously monitored?',
                        7  => 'Is the chemical room temperature range adequate for insecticide products?',
                        8  => 'Is the storage area adequately ventilated?',
                        9  => 'Is there a separate area for the mixing of test items?',
                        10 => 'Are test item dilutions labelled to ensure proper identification of test item?',
                        11 => 'Is the SOP for reception, registration and storage of materials followed?',
                        12 => 'Is the reception of test/control/reference items documented (test item reception book)?',
                        13 => 'Are Test/control/reference substances properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions, MSDS)?',
                        14 => 'Are there records of test/control/reference items usage?',
                        15 => 'Is there a procedure for disposal of test items?',
                        16 => 'Is test item disposal documented?',
                    ],
                ],
                'e' => [
                    'title' => 'Experimental Huts – SITE 1',
                    'questions' => [
                        1  => 'Is the experimental hut site secured?',
                        2  => 'Are the huts clean and well maintained?',
                        3  => 'Are the gutters filled with clean water?',
                        4  => 'Are there any cracks on the walls of the huts?',
                        5  => 'Are all the huts locked?',
                        6  => 'Is the security guard available?',
                        7  => 'Are the hut surroundings clean and well maintained?',
                        8  => 'Are the toilets clean and well maintained?',
                        9  => 'Are the preparation rooms clean and well maintained?',
                        10 => 'Is the temperature in the huts being recorded?',
                        11 => 'Are the checklist being filled on a daily basis?',
                        12 => 'Are the cows well maintained?',
                        13 => 'Are there any issues to be addressed?',
                    ],
                ],
                'f' => [
                    'title' => 'Experimental Huts – SITE 2',
                    'questions' => [
                        1  => 'Is the experimental hut site secured?',
                        2  => 'Are the huts clean and well maintained?',
                        3  => 'Are the gutters filled with clean water?',
                        4  => 'Are there any cracks on the walls of the huts?',
                        5  => 'Are all the huts locked?',
                        6  => 'Is the security guard available?',
                        7  => 'Are the hut surroundings clean and well maintained?',
                        8  => 'Are the toilets clean and well maintained?',
                        9  => 'Are the preparation rooms clean and well maintained?',
                        10 => 'Is the temperature in the huts being recorded?',
                        11 => 'Are the checklist being filled on a daily basis?',
                        12 => 'Are the cows well maintained?',
                        13 => 'Are there any issues to be addressed?',
                    ],
                ],
                'g' => [
                    'title' => 'Experimental Huts – SITE 3',
                    'questions' => [
                        1  => 'Is the experimental hut site secured?',
                        2  => 'Are the huts clean and well maintained?',
                        3  => 'Are the gutters filled with clean water?',
                        4  => 'Are there any cracks on the walls of the huts?',
                        5  => 'Are all the huts locked?',
                        6  => 'Is the security guard available?',
                        7  => 'Are the hut surroundings clean and well maintained?',
                        8  => 'Are the toilets clean and well maintained?',
                        9  => 'Are the preparation rooms clean and well maintained?',
                        10 => 'Is the temperature in the huts being recorded?',
                        11 => 'Are the checklist being filled on a daily basis?',
                        12 => 'Are the cows well maintained?',
                        13 => 'Are there any issues to be addressed?',
                    ],
                ],
                'h' => [
                    'title' => 'Insectary',
                    'questions' => [
                        1  => 'Is there a designated personnel responsible for the management of the insectary and the animal house?',
                        2  => 'Are copies of personnel file available and up to date?',
                        3  => 'Is the insectary neat and well kept?',
                        4  => 'Is access to the insectary rooms limited?',
                        5  => 'Are surveillance video cameras in working order?',
                        6  => 'Is the organogram available?',
                        7  => 'Is the floor plan available?',
                        8  => 'Is the insectary policy manual available?',
                        9  => 'Are SOPs related to insectary activities available?',
                        10 => 'Is there a calibration plan for insectary equipment?',
                        11 => 'Are all equipment sheets regularly filled and signed by unit supervisor?',
                        12 => 'Are insectary materials well arranged?',
                        13 => 'Are appropriate dress procedures followed?',
                        14 => 'Are insectary coats clean and well arranged?',
                        15 => 'Is the water under the tables regularly changed and clean?',
                        16 => 'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                        17 => 'Are adult mosquito separated from larvae?',
                        18 => 'Are adult mosquito rooms environmentally controlled and monitored?',
                        19 => 'Are the mosquito cages labelled accordingly and well arranged?',
                        20 => 'Are breeding cages separated from test cages?',
                        21 => 'Is resistance test performed for each mosquito strain?',
                        22 => 'Is the resistance status report provided to the FM and satisfactory?',
                        23 => 'Are mosquito production sheets regularly filled?',
                        24 => 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to?',
                        25 => 'Are all sheets signed by the unit supervisor?',
                    ],
                ],
                'i' => [
                    'title' => 'Animal House',
                    'questions' => [
                        1  => 'Is there a person responsible for the management of the animal house?',
                        2  => 'Is the animal house neat?',
                        3  => 'Is access to the animal house limited?',
                        4  => 'Are the animal cages clearly labelled?',
                        5  => 'Is each animal cage locked?',
                        6  => 'Are breeding animal separated from test animal?',
                        7  => 'Are animal house materials well arranged?',
                        8  => 'Are appropriate dress procedures followed?',
                        9  => 'Are SOPs related to animal house activities available?',
                        10 => 'Are animal maintenance forms regularly filled and signed?',
                        11 => 'Is there a documented procedure for releasing animals for test or blood feeding?',
                        12 => 'Is the animal house regularly disinfected?',
                    ],
                ],
            ],
        ];
    }

    /**
     * Définition du formulaire Facility Inspection Checklist (QA-PR-1-001A/06).
     * 15 sections (A–O), chacune avec ses questions et un champ commentaires.
     */
    private static function facilityForm(): array
    {
        return [
            'model'   => \App\Models\Pro_Cl_FacilityInspection::class,
            'letter'  => 'FI',
            'title'   => 'Facility Inspection Checklist (Main Facility)',
            'doc_ref' => 'QA-PR-1-001A/06',
            'sections' => [
                'a' => [
                    'title' => 'Administration',
                    'questions' => [
                        1  => 'Have all non-conformances raised from previous facility inspection been corrected?',
                        2  => 'Does the Facility have a current GLP compliance certificate?',
                        3  => 'Is the organizational chart up to date and available in every section?',
                        4  => 'Does the organization chart adequately describe reporting structure?',
                        5  => 'Is the Facility Manager clearly identified?',
                        6  => 'Is the QA Manager clearly identified?',
                        7  => 'Are Study Director(s) clearly identified?',
                        8  => 'Is the Data Manager clearly identified?',
                        9  => 'Is the Archivist clearly identified?',
                        10 => 'Is the Administration clearly identified?',
                        11 => 'Is a floor plan of the Facility up to date and available?',
                        12 => 'Is the Master schedule up to date and available?',
                        13 => 'Is the List of Projects up to date and available?',
                        14 => 'Is the Facility Quality Manual up to date and available?',
                        15 => 'Is there a system for keeping personnel records?',
                        16 => 'Is the personnel records accessible to everyone?',
                        17 => 'Does each staff have a file?',
                        18 => 'Does each staff have a work contract that is up to date?',
                        19 => 'Are the CVs up to date with detailed information, signed and available for all personnel?',
                        20 => 'Are current job descriptions signed and available for all personnel?',
                        21 => 'Are there procedures/policies covering staff training?',
                        22 => 'Is there evidence of training for each staff?',
                        23 => 'Are training records current for all personnel?',
                        24 => 'Are training records reviewed periodically as per SOP?',
                        25 => 'Is a training programme for the current year available?',
                        26 => 'Are GLP personnel files maintained after departures of staff?',
                    ],
                ],
                'b' => [
                    'title' => 'Document Control',
                    'questions' => [
                        1  => 'Is there a document control team?',
                        2  => 'Is there someone responsible for the management and distribution of SOPs?',
                        3  => 'Is there an index for SOPs and other controlled documents?',
                        4  => 'Is there an SOP and document control review plan?',
                        5  => 'Are controlled documents reviewed every 2 years?',
                        6  => 'Are all controlled documents (SOPs, Sheets, and Policy Manuals) up to date?',
                        7  => 'Is there an SOP for managing SOPs?',
                        8  => 'Is there an SOP for document control?',
                        9  => 'All controlled documents available (SOPs, Sheets, and Policy Manuals) in each section as appropriate?',
                        10 => 'Do SOPs accurately reflect current procedures?',
                        11 => 'Are all SOPs signed, dated and approved by the Facility Manager?',
                        12 => 'Does each SOP have the version number, the author, the list of appendices, and the number of pages over the total number of pages?',
                        13 => 'Are appendix attached to all SOPs?',
                        14 => 'Were the changes brought to previous version mentioned in current version of SOPs and other controlled documents?',
                        15 => 'Are there procedures in place for replacing revised SOPs or other controlled documents and ensuring that old SOPs or other controlled document are not available for use (removed from circulation)?',
                    ],
                ],
                'c' => [
                    'title' => 'Bioassay Laboratory',
                    'questions' => [
                        1  => 'Is the bioassay laboratory secured from unauthorised access?',
                        2  => 'Is the work area neat?',
                        3  => 'Is the water under the tables clean and changed regularly?',
                        4  => 'Are laboratory tools safely secured and stored when not in use?',
                        5  => 'Are racks labelled accordingly providing detailed information and well arranged?',
                        6  => 'Are insecticide product waste disposed separately from regular waste?',
                        7  => 'Is the laboratory environmentally controlled?',
                        8  => 'Is the laboratory temperature monitored?',
                        9  => 'Are appropriate dress procedures followed?',
                        10 => 'Are lab coats clean and well arranged?',
                        11 => 'Are SOPs related to laboratory activities up to date, signed and available?',
                    ],
                ],
                'd' => [
                    'title' => 'Biomolecular Room',
                    'questions' => [
                        1 => 'Is access to the biomolecular room limited?',
                        2 => 'Is the biomolecular room clean and well organised?',
                        3 => 'Is the biomolecular room environmentally controlled?',
                        4 => 'Is the biomolecular room temperature monitored?',
                        5 => 'Are the equipment in the biomolecular room clean?',
                    ],
                ],
                'e' => [
                    'title' => 'Shaker-Bath room and LLIN Washing area',
                    'questions' => [
                        1 => 'Is the shaker-bath room clean and well organised?',
                        2 => 'Is the shaker-bath room free from water spillage?',
                        3 => 'Are the equipment sheets for all shaker-baths up to date and available?',
                        4 => 'Is the LLIN washing area neat and free from water spillage?',
                    ],
                ],
                'f' => [
                    'title' => 'Chemical & Potter tower Room',
                    'questions' => [
                        1  => 'Is there someone responsible for the management of the chemical room?',
                        2  => 'Is there limited access to chemical storage room?',
                        3  => 'Is the chemical storage room neat and organized?',
                        4  => 'Are there separate areas for storage of test/control/reference items?',
                        5  => 'Is the chemical storage room environmentally controlled?',
                        6  => 'Is the chemical room temperature continuously monitored?',
                        7  => 'Is the chemical room temperature range adequate for insecticide products?',
                        8  => 'Is the storage area adequately ventilated?',
                        9  => 'Is there a separate area for the mixing of test items e.g. fume hood?',
                        10 => 'Is there a separate area for spraying insecticides on substrates?',
                        11 => 'Is there an extraction fan in the potter tower room?',
                        12 => 'Are Test/control/reference substances and dilutions properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions) to ensure proper identification of test items?',
                        13 => 'Is the SOP for reception, registration and storage of materials followed?',
                        14 => 'Is the reception of test/control/reference items documented?',
                        15 => 'Are there records of MSDS and Chemical analysis certificates of test items?',
                        16 => 'Are there records of test/control/reference items usage?',
                        17 => 'Is there a procedure for disposal of test items?',
                        18 => 'Is test item disposal documented?',
                        19 => 'Is there a calendar to update list of chemical products and mosquito nets?',
                    ],
                ],
                'g' => [
                    'title' => 'Safety (changing) room',
                    'questions' => [
                        1 => 'Is there someone responsible for the management of the safety (changing) room?',
                        2 => 'Is the safety room separated from other sections?',
                        3 => 'Is there limited access to the safety room?',
                        4 => 'Is the locker for storage of facemask adequately locked?',
                        5 => 'Are safety materials adequate for use?',
                        6 => 'Is there a safety procedure for the Facility?',
                        7 => 'Is there a calendar for safety inspections?',
                    ],
                ],
                'h' => [
                    'title' => 'Storage and untreated block rooms',
                    'questions' => [
                        1 => 'Is there limited access to storage and untreated block rooms?',
                        2 => 'Are the storage room and the untreated block room neat and organized?',
                        3 => 'Is the untreated block room environmentally controlled?',
                        4 => 'Is the untreated block room temperature continuously monitored?',
                        5 => 'Are untreated blocks labelled for easy identification?',
                        6 => 'Is there a separate area for the mixing of test items e.g. fume hood?',
                    ],
                ],
                'i' => [
                    'title' => 'Net storage room and expired products Room',
                    'questions' => [
                        1 => 'Is there limited access to net storage room and the expired products room?',
                        2 => 'Are the net and expired products storage rooms neat and organized?',
                        3 => 'Is the net storage room environmentally controlled?',
                        4 => 'Is the net room temperature continuously monitored?',
                    ],
                ],
                'j' => [
                    'title' => 'Equipment',
                    'questions' => [
                        1  => 'Is there a person designated as responsible for equipment?',
                        2  => 'Are equipment uniquely identified and included on the equipment inventory list?',
                        3  => 'Is the equipment inventory up to date?',
                        4  => 'Does each equipment have a file?',
                        5  => 'Are Equipment instructions manual available and easily accessible?',
                        6  => 'Are Equipment SOPs available and easily accessible and for each piece of equipment?',
                        7  => 'Are calibration certificates available for each equipment?',
                        8  => 'Are all equipment calibration certificate up to date?',
                        9  => 'Is the Equipment calibration programme defined and regularly followed?',
                        10 => 'Are Usage/maintenance/calibration/fault report sheets accessible and regularly filled in?',
                        11 => 'Are Maintenance logs on equipment up-to-date?',
                        12 => 'Is the equipment history regularly written in the log book?',
                        13 => 'Are equipment cleaned after use?',
                        14 => 'Do all equipment appear to be in good repair?',
                        15 => 'Are equipment adequately stored when not used?',
                    ],
                ],
                'k' => [
                    'title' => 'Staff Offices & Buildings',
                    'questions' => [
                        1  => 'Are different sections within the Facility clearly defined?',
                        2  => 'Are all entry ways secured from unauthorized access?',
                        3  => 'Are offices clean and well maintained?',
                        4  => 'Is the entire building clean and well maintained on daily basis?',
                        5  => 'Are all floors free of liquids to avoid slips and falls?',
                        6  => 'Is there any housekeeping issues that need to be addressed?',
                        7  => 'Are all plugs and cords in good condition?',
                        8  => 'Are there electrical switches, switch plates or receptacles that are cracked, broken or have exposed contacts?',
                        9  => 'Are all electrical circuit breakers identified?',
                        10 => 'Is there any circuit breakers regularly tripping?',
                        11 => 'Are surveillance video cameras working?',
                        12 => 'Is the building equipped with fire extinguishers?',
                        13 => 'Are there any security issues to be addressed?',
                    ],
                ],
                'l' => [
                    'title' => 'Data Management',
                    'questions' => [
                        1  => 'Is there a personnel responsible for the development, validation, operation and maintenance of computerised systems?',
                        2  => 'Is the data entry room secured from unauthorised access?',
                        3  => 'Is the data entry room equipped with an extinguisher?',
                        4  => 'Is the data entry room clean and well organised?',
                        5  => 'Is the data entry room environmentally controlled and monitored?',
                        6  => 'Is the temperature or humidity reported when out of range?',
                        7  => 'Is there a documented policy for the recording and management of data?',
                        8  => 'Are there SOPs for data management and are they all available?',
                        9  => 'Are all data entry computers protected by a password system?',
                        10 => 'Are computers regularly maintained?',
                        11 => 'Is the maintenance of computerised systems used in GLP studies up to date?',
                        12 => 'Are all computers equipped with an up-to-date anti-virus programme?',
                        13 => 'Are peripheral components of computer hardware in good state?',
                        14 => 'Is there records of any problems or fault detected and any remedial action taken during operation of the system?',
                        15 => 'Are there computers taken out of the system?',
                        16 => 'Is the server regularly maintained?',
                        17 => 'Is there a backup system in place where data are secured?',
                        18 => 'Is data recorded directly, legibly and indelibly?',
                        19 => 'Are all data signed and dated at the time of entry?',
                        20 => 'Are all data double entered?',
                        21 => 'Are alterations to data such that they do not obscure the original and indicate the person making the alteration, the date of the alteration and the reason for the alteration using the appropriate error correction code where appropriate?',
                        22 => 'Are computer systems used to generate study data?',
                        23 => 'Are computerised systems regularly validated?',
                        24 => 'Is the frequency for validation of computerised system defined?',
                        25 => 'Are there any issues to be addressed?',
                    ],
                ],
                'm' => [
                    'title' => 'Archive',
                    'questions' => [
                        1  => 'Is the archive room secured from unauthorised access?',
                        2  => 'Is the archive room equipped with an extinguisher?',
                        3  => 'Are the facilities secured and resistant to fire?',
                        4  => 'Is the archive room neat and well organised?',
                        5  => 'Is the archive room environmentally controlled?',
                        6  => 'Is the archive room temperature monitored?',
                        7  => 'Is there a designated archivist?',
                        8  => 'Is there a deputy archivist?',
                        9  => 'Are non GLP files separated from GLP files?',
                        10 => 'Are all cabinets locked?',
                        11 => 'Are there documented procedures for the submission of data to and the withdrawal of data from archive?',
                        12 => 'Are there SOPs for activities performed in the archive and are they available?',
                        13 => 'Is the material indexed to expedite retrieval?',
                        14 => 'Is the Archive logbook regularly filled in?',
                        15 => 'Is the archivist made aware of the contents of study files to be archived?',
                        16 => 'Are completed studies project boxes followed up for archive?',
                    ],
                ],
                'n' => [
                    'title' => 'Insectary and Annex',
                    'questions' => [
                        1  => 'Is there a designated personnel responsible for the management of the insectary and the animal house?',
                        2  => 'Are copies of personnel file available and up to date?',
                        3  => 'Is the insectary neat and well kept?',
                        4  => 'Is access to the insectary rooms limited?',
                        5  => 'Are surveillance video cameras in working order?',
                        6  => 'Is the organogram available?',
                        7  => 'Is the floor plan available?',
                        8  => 'Is the insectary policy manual available?',
                        9  => 'Are SOPs related to insectary activities available?',
                        10 => 'Is there a calibration plan for insectary equipment?',
                        11 => 'Are all equipment sheets regularly filled and signed by unit supervisor?',
                        12 => 'Are insectary materials well arranged?',
                        13 => 'Are appropriate dress procedures followed?',
                        14 => 'Are insectary coats clean and well arranged?',
                        15 => 'Is the water under the tables regularly changed and clean?',
                        16 => 'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                        17 => 'Are adult mosquito separated from larvae?',
                        18 => 'Are adult mosquito rooms environmentally controlled and monitored?',
                        19 => 'Are the mosquito cages labelled accordingly and well arranged?',
                        20 => 'Are breeding cages separated from test cages?',
                        21 => 'Is resistance test performed for each mosquito strain?',
                        22 => 'Is the resistance status report provided to the FM and satisfactory?',
                        23 => 'Are mosquito production sheets regularly filled?',
                        24 => 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to?',
                        25 => 'Are all sheets signed by the unit supervisor?',
                    ],
                ],
                'o' => [
                    'title' => 'Animal House',
                    'questions' => [
                        1  => 'Is there a person responsible for the management of the animal house?',
                        2  => 'Is the animal house neat?',
                        3  => 'Is access to the animal house limited?',
                        4  => 'Are the animal cages clearly labelled?',
                        5  => 'Is each animal cage locked?',
                        6  => 'Are breeding animal separated from test animal?',
                        7  => 'Are animal house materials well arranged?',
                        8  => 'Are appropriate dress procedures followed?',
                        9  => 'Are SOPs related to animal house activities available?',
                        10 => 'Are animal maintenance forms regularly filled and signed?',
                        11 => 'Is there a documented procedure for releasing animals for test or blood feeding?',
                        12 => 'Is the animal house regularly disinfected?',
                    ],
                ],
            ],
        ];
    }

    /**
     * Send sign-request notifications to all relevant parties when an inspection completes.
     * Called once per inspection, the first time date_performed is set.
     */
    private function notifyInspectionComplete(Pro_QaInspection $inspection): void
    {
        $project  = $inspection->project;
        $code     = $project?->project_code ?? "#{$inspection->project_id}";
        $type     = $inspection->type_inspection ?? 'Inspection';
        $reportUrl = route('checklist.report', $inspection->id);

        $title = "Inspection completed — {$code}";
        $body  = "\"{$type}\" for project {$code} has been completed. Please review and sign the QA Unit Report.";

        $notified = [];

        // QA Inspector
        if ($inspection->inspector?->user_id) {
            AppNotification::send($inspection->inspector->user_id, 'signature_requested', $title, $body, $reportUrl, 'bi-pen-fill');
            $notified[] = $inspection->inspector->user_id;
        }

        // Study Director (via appointment form)
        $appt = $project?->studyDirectorAppointmentForm;
        if ($appt && $appt->studyDirector?->user_id && !in_array($appt->studyDirector->user_id, $notified)) {
            AppNotification::send($appt->studyDirector->user_id, 'signature_requested', $title, $body, $reportUrl, 'bi-pen-fill');
            $notified[] = $appt->studyDirector->user_id;
        }

        // QA Managers
        User::where('role', 'qa_manager')->each(function ($u) use ($title, $body, $reportUrl, &$notified) {
            if (!in_array($u->id, $notified)) {
                AppNotification::send($u->id, 'signature_requested', $title, $body, $reportUrl, 'bi-pen-fill');
                $notified[] = $u->id;
            }
        });

        // Facility Managers
        User::where('role', 'facility_manager')->each(function ($u) use ($title, $body, $reportUrl, &$notified) {
            if (!in_array($u->id, $notified)) {
                AppNotification::send($u->id, 'signature_requested', $title, $body, $reportUrl, 'bi-pen-fill');
                $notified[] = $u->id;
            }
        });
    }
}
