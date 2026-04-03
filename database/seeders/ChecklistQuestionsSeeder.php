<?php

namespace Database\Seeders;

use App\Models\ClTemplate;
use App\Models\ClSection;
use App\Models\ClQuestion;
use Illuminate\Database\Seeder;

/**
 * Populates cl_templates / cl_sections / cl_questions with all hardcoded
 * checklist questions from the application.
 * Safe to re-run (uses firstOrCreate / updateOrCreate).
 */
class ChecklistQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedQaActivitiesChecklist();
        $this->seedQaReviewInspection();
        $this->seedAmendmentDeviationChecklist();
        $this->seedStudyProtocolInspection();
        $this->seedStudyReportInspection();
        $this->seedDataQualityInspection();
        $this->seedFacilityInspectionMain();
        $this->seedFacilityInspectionCove();
        $this->seedProcessInspection();
        $this->seedCriticalPhaseChecklists();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QA Activities Checklist  (checkbox + date + text)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedQaActivitiesChecklist(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'qa_activities'], [
            'name'           => 'QA Activities Checklist',
            'reference_code' => 'QA-PR-1-011/05',
            'version'        => '1.0',
            'category'       => 'qa',
            'description'    => '20-item checklist tracking QA activities for GLP studies, per project.',
        ]);

        $sec = ClSection::updateOrCreate(
            ['template_id' => $tpl->id, 'code' => 'main'],
            ['letter' => '', 'title' => 'QA Activities', 'sort_order' => 1, 'form_type' => 'checkbox_date_text']
        );

        $questions = [
            1  => 'Study Protocol received from SD',
            2  => 'Study Protocol inspection performed by QA Manager / Personnel',
            3  => 'Study Protocol Inspection findings reported to Facility Manager and SD',
            4  => 'Study Protocol signed by QA Manager / Personnel',
            5  => 'Copy of approved protocol received from SD',
            6  => 'Critical phase agreement meeting with SD',
            7  => 'Study inspection programme established by QA Manager / Personnel',
            8  => 'Critical phases inspections performed by QA Manager / Personnel',
            9  => 'Data Quality Inspections performed by QA Manager / Personnel',
            10 => 'Copies of Amendment / Deviation forms received from SD',
            11 => 'Amendments / Deviations inspected by QA Manager / Personnel',
            12 => 'Amendments / Deviations inspections findings reported to Facility Manager and SD',
            13 => 'Copy of draft study report received from SD',
            14 => 'Draft study report inspected by QA Manager / Personnel',
            15 => 'Draft study report inspection findings reported to FM and SD',
            16 => 'Copy of final study report received from SD',
            17 => 'Final study report inspected by QA Manager / Personnel',
            18 => 'Final study report inspection findings reported to FM and SD',
            19 => 'QA Statement signed by QA Manager / Personnel',
            20 => 'Archiving of QA file',
        ];

        foreach ($questions as $num => $text) {
            ClQuestion::updateOrCreate(
                ['section_id' => $sec->id, 'item_number' => (string)$num],
                ['text' => $text, 'response_type' => 'checkbox_date_text', 'sort_order' => $num]
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QA Review Inspection (Facility Manager)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedQaReviewInspection(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'qa_review'], [
            'name'           => 'QA Review Inspection',
            'reference_code' => 'QA-PR-1-016/04',
            'version'        => '1.0',
            'category'       => 'qa',
            'description'    => 'Biennial QA Review Checklist conducted by the Facility Manager.',
        ]);

        $sections = [
            'I'     => ['title' => 'QA Staff Records & Trainings',                   'sort' => 1, 'questions' => [
                1 => 'Are personnel records of QA staff complete and up to date?',
                2 => 'Have all QA staff been trained in line with their respective training logs?',
                3 => 'Are the training logs of each staff up to date?',
                4 => 'Did staff achieve competence for each training?',
                5 => 'Where there any delays in staff trainings?',
            ]],
            'II'    => ['title' => 'QA Manuals & SOPs',                              'sort' => 2, 'questions' => [
                1 => 'Are all QA Manuals and SOPs up to date?',
                2 => 'Are all QA Manuals and SOPs available to all QA staff?',
                3 => 'Can QA staff confidently explain the procedures in the manuals and SOPs?',
                4 => 'Is the Master Schedule regularly maintained by QA and made available to the Facility manager?',
            ]],
            'III_A' => ['title' => 'QA Activities — A. Facility & Process Inspections', 'subtitle' => 'A- Facility & Process Inspections', 'sort' => 3, 'display_style' => 'subsection', 'questions' => [
                1 => 'Is the QA calendar available and known by all QA staff?',
                2 => 'Is the inspection calendar respected? If not explain any delays',
                3 => 'Are there documented reports of each QA inspection?',
                4 => 'Are reports signed and staff responsible for corrective actions fully informed?',
                5 => 'Does QA follow-up on corrective actions? Is this documented regularly?',
            ]],
            'III_B' => ['title' => 'QA Activities — B. Study-based Inspections',    'subtitle' => 'B- Study-based Inspections', 'sort' => 4, 'display_style' => 'subsection', 'questions' => [
                1 => 'Are all GLP protocols inspected and signed by QAM?',
                2 => 'Are critical phases for GLP studies selected according to procedures of the Facility?',
                3 => 'Are critical phases performed as planned?',
                4 => 'Are QA findings of study-based inspections promptly reported to the Study Director & FM?',
                5 => 'Does QA follow-up on corrective actions from study-based inspections?',
            ]],
            'III_C' => ['title' => 'QA Activities — C. QA and Study Reports',       'subtitle' => 'C- QA and Study Reports', 'sort' => 5, 'display_style' => 'subsection', 'questions' => [
                1 => 'Has QA included a statement in each GLP report?',
                2 => 'Does QA follow-up on study deviations and amendments to ensure they are included in final reports of GLP studies?',
            ]],
            'IV'    => ['title' => 'Continuous Progress of QA',                      'sort' => 6, 'questions' => [
                1 => 'Are external QA trainings up to date?',
                2 => 'Are assessment scores achieved acceptable?',
                3 => 'Is there a plan of smooth succession should the QA Manager be unavoidably absent?',
            ]],
        ];

        foreach ($sections as $code => $def) {
            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => $code],
                [
                    'letter'        => in_array($code, ['III_A','III_B','III_C']) ? substr($code, 4) : $code,
                    'title'         => $def['title'],
                    'subtitle'      => $def['subtitle'] ?? null,
                    'display_style' => $def['display_style'] ?? 'normal',
                    'sort_order'    => $def['sort'],
                    'form_type'     => 'yes_no',
                ]
            );

            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => 'yes_no', 'sort_order' => $num]
                );
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Amendment / Deviation Inspection (QA-PR-1-004/06)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedAmendmentDeviationChecklist(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'amendment_deviation'], [
            'name'           => 'Amendment & Deviations Inspection',
            'reference_code' => 'QA-PR-1-004/06',
            'version'        => '1.0',
            'category'       => 'protocol',
            'description'    => 'Single-section checklist for Study Protocol Amendment/Deviation Inspections.',
        ]);

        $sec = ClSection::updateOrCreate(
            ['template_id' => $tpl->id, 'code' => 'main'],
            ['letter' => 'AD', 'title' => 'Amendment & Deviations', 'sort_order' => 1, 'form_type' => 'yes_no_na']
        );

        $questions = [
            1 => 'Is there an amendment/ deviation N°',
            2 => 'Is the number of pages over the total number of pages visible?',
            3 => 'Is the study code written?',
            4 => 'Is the study title stated?',
            5 => 'Was the amendment/ deviation described appropriately?',
            6 => 'Is the reason for the amendment/ deviation described stated?',
            7 => 'Is the impact on the study described?',
            8 => 'Was the amendment/ deviation signed by the Study Director?',
        ];

        foreach ($questions as $num => $text) {
            ClQuestion::updateOrCreate(
                ['section_id' => $sec->id, 'item_number' => (string)$num],
                ['text' => $text, 'response_type' => 'yes_no_na', 'sort_order' => $num]
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Study Protocol Inspection  (6 sections A–F)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedStudyProtocolInspection(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'study_protocol'], [
            'name'     => 'Study Protocol Inspection',
            'version'  => '1.0',
            'category' => 'protocol',
        ]);

        $sections = [
            'sp-a' => ['letter' => 'A', 'title' => 'General',                        'sort' => 1, 'type' => 'yes_no_na', 'questions' => [
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
            ]],
            'sp-b' => ['letter' => 'B', 'title' => 'Test System',                    'sort' => 2, 'type' => 'yes_no_na', 'questions' => [
                1 => 'Is the Test system described?',
                2 => 'Is the Source of test system stated?',
                3 => 'Are the Characteristics/status of test system described?',
                4 => 'Is the number of test system defined?',
            ]],
            'sp-c' => ['letter' => 'C', 'title' => 'Test, Control & Reference Articles', 'sort' => 3, 'type' => 'yes_no_na', 'questions' => [
                1 => 'Is the Name, CAS number or code number provided?',
                2 => 'Is the Supplier of test, control & ref. substance stated?',
                3 => 'Are Storage conditions information for the test, control, ref. substance stated?',
                4 => 'Is the Carrier or vehicle identified?',
                5 => 'Is the Concentration of test material stated?',
                6 => 'Is the frequency of applications stated?',
            ]],
            'sp-d' => ['letter' => 'D', 'title' => 'Equipment',                      'sort' => 4, 'type' => 'yes_no_na', 'questions' => [
                1 => 'Are the Equipment needed for study available?',
                2 => 'Are Equipment calibrated and well maintained?',
            ]],
            'sp-e' => ['letter' => 'E', 'title' => 'SOPs',                           'sort' => 5, 'type' => 'yes_no_na', 'questions' => [
                1 => 'Are SOPs to be used listed in the study protocol?',
                2 => 'Are the SOPs to be used in the study available?',
                3 => 'Have the SOPs been approved?',
                4 => 'Have the study personnel read, understood the SOPs to be used in the study?',
            ]],
            'sp-f' => ['letter' => 'F', 'title' => 'Study Personnel',                'sort' => 6, 'type' => 'staff_training', 'questions' => [
                1 => 'Are study personnel appointed for study sufficient?',
            ]],
        ];

        foreach ($sections as $code => $def) {
            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => $code],
                ['letter' => $def['letter'], 'title' => $def['title'], 'sort_order' => $def['sort'], 'form_type' => $def['type']]
            );
            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => $def['type'] === 'staff_training' ? 'staff_training' : 'yes_no_na', 'sort_order' => $num]
                );
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Study Report Inspection  (5 sections A–E)  QA-PR-1-005/06
    // ─────────────────────────────────────────────────────────────────────────
    private function seedStudyReportInspection(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'study_report'], [
            'name'           => 'Study Report Inspection',
            'reference_code' => 'QA-PR-1-005/06',
            'version'        => '1.0',
            'category'       => 'protocol',
        ]);

        $sections = [
            'sr-a' => ['letter' => 'A', 'title' => 'General',                         'sort' => 1, 'questions' => [
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
            ]],
            'sr-b' => ['letter' => 'B', 'title' => 'Test, Control and Reference Substances', 'sort' => 2, 'questions' => [
                1 => 'Is the Name, CAS or code number of test and control or reference substance mentioned in the final report?',
                2 => 'Is the supplier of test and control or reference substance stated?',
                3 => 'Are storage conditions information for test and control or reference substance monitored and mentioned?',
            ]],
            'sr-c' => ['letter' => 'C', 'title' => 'Test System Description',         'sort' => 3, 'questions' => [
                1 => 'Are the following described about the test system? (a-Strain, b-Age, c-Source, d-Resistance status, e-Number)',
                2 => 'Is the test condition (temperature) mentioned?',
            ]],
            'sr-d' => ['letter' => 'D', 'title' => 'Data Management and Statistical Analysis', 'sort' => 4, 'questions' => [
                1 => 'Is there evidence of the validation of the software used for data entry and is it included in the final report?',
                2 => 'Are all data double entered?',
                3 => 'Are all relevant raw data reported and omissions (if applicable) explained?',
                4 => 'Is there a description of: a-Summary of analysis of data, b-Conclusions drawn from data',
            ]],
            'sr-e' => ['letter' => 'E', 'title' => 'Quality Assurance',               'sort' => 5, 'questions' => [
                1 => 'Are all QA audit/inspection reports complete with findings addressed?',
                2 => 'Was a QA statement issued and included in the report?',
                3 => 'Is a Quality Assurance statement accurate, complete, signed and dated by the QA Manager?',
                4 => 'Is the QA file for the Study complete?',
            ]],
        ];

        foreach ($sections as $code => $def) {
            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => $code],
                ['letter' => $def['letter'], 'title' => $def['title'], 'sort_order' => $def['sort'], 'form_type' => 'yes_no_na']
            );
            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => 'yes_no_na', 'sort_order' => $num]
                );
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Data Quality Inspection  (5 sections A–E)  QA-PR-1-018/03
    // ─────────────────────────────────────────────────────────────────────────
    private function seedDataQualityInspection(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'data_quality'], [
            'name'           => 'Data Quality Inspection',
            'reference_code' => 'QA-PR-1-018/03',
            'version'        => '1.0',
            'category'       => 'protocol',
        ]);

        $sections = [
            'dq-a' => ['letter' => 'A', 'title' => 'Staff Training',                 'sort' => 1, 'type' => 'yes_no_na', 'questions' => [
                '1' => 'Are all staff trained on GLP?',
                '2' => 'Are all key study personnel trained on Data Management?',
                '3' => 'Are all key study personnel trained on study related activities?',
            ]],
            'dq-b' => ['letter' => 'B', 'title' => 'Computerised Systems and Softwares Validation', 'sort' => 2, 'type' => 'yes_no_na', 'questions' => [
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
            ]],
            'dq-c' => ['letter' => 'C', 'title' => 'Data Validity',                  'sort' => 3, 'type' => 'dual_verification', 'questions' => [
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
            ]],
            'dq-d' => ['letter' => 'D', 'title' => 'Data Sheet Information',         'sort' => 4, 'type' => 'dual_verification', 'questions' => [
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
            ]],
            'dq-e' => ['letter' => 'E', 'title' => 'Study Box',                      'sort' => 5, 'type' => 'study_box', 'questions' => [
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
            ]],
        ];

        foreach ($sections as $code => $def) {
            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => $code],
                ['letter' => $def['letter'], 'title' => $def['title'], 'sort_order' => $def['sort'], 'form_type' => $def['type']]
            );
            $rtype = match($def['type']) {
                'dual_verification' => 'yes_no_na',
                'study_box'         => 'study_box_item',
                default             => 'yes_no_na',
            };
            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => $rtype, 'sort_order' => array_search($num, array_keys($def['questions'])) + 1]
                );
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Facility Inspection — Main  (QA-PR-1-001A/06)  15 sections A–O
    // ─────────────────────────────────────────────────────────────────────────
    private function seedFacilityInspectionMain(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'facility_main'], [
            'name'           => 'Facility Inspection Checklist (Main Facility)',
            'reference_code' => 'QA-PR-1-001A/06',
            'version'        => '1.0',
            'category'       => 'facility',
        ]);

        $sections = [
            'a' => ['letter' => 'A', 'title' => 'Administration', 'sort' => 1, 'questions' => [
                1=>  'Have all non-conformances raised from previous facility inspection been corrected?',
                2=>  'Does the Facility have a current GLP compliance certificate?',
                3=>  'Is the organizational chart up to date and available in every section?',
                4=>  'Does the organization chart adequately describe reporting structure?',
                5=>  'Is the Facility Manager clearly identified?',
                6=>  'Is the QA Manager clearly identified?',
                7=>  'Are Study Director(s) clearly identified?',
                8=>  'Is the Data Manager clearly identified?',
                9=>  'Is the Archivist clearly identified?',
                10=> 'Is the Administration clearly identified?',
                11=> 'Is a floor plan of the Facility up to date and available?',
                12=> 'Is the Master schedule up to date and available?',
                13=> 'Is the List of Projects up to date and available?',
                14=> 'Is the Facility Quality Manual up to date and available?',
                15=> 'Is there a system for keeping personnel records?',
                16=> 'Is the personnel records accessible to everyone?',
                17=> 'Does each staff have a file?',
                18=> 'Does each staff have a work contract that is up to date?',
                19=> 'Are the CVs up to date with detailed information, signed and available for all personnel?',
                20=> 'Are current job descriptions signed and available for all personnel?',
                21=> 'Are there procedures/policies covering staff training?',
                22=> 'Is there evidence of training for each staff?',
                23=> 'Are training records current for all personnel?',
                24=> 'Are training records reviewed periodically as per SOP?',
                25=> 'Is a training programme for the current year available?',
                26=> 'Are GLP personnel files maintained after departures of staff?',
            ]],
            'b' => ['letter' => 'B', 'title' => 'Document Control', 'sort' => 2, 'questions' => [
                1=>  'Is there a document control team?',
                2=>  'Is there someone responsible for the management and distribution of SOPs?',
                3=>  'Is there an index for SOPs and other controlled documents?',
                4=>  'Is there an SOP and document control review plan?',
                5=>  'Are controlled documents reviewed every 2 years?',
                6=>  'Are all controlled documents (SOPs, Sheets, and Policy Manuals) up to date?',
                7=>  'Is there an SOP for managing SOPs?',
                8=>  'Is there an SOP for document control?',
                9=>  'All controlled documents available (SOPs, Sheets, and Policy Manuals) in each section as appropriate?',
                10=> 'Do SOPs accurately reflect current procedures?',
                11=> 'Are all SOPs signed, dated and approved by the Facility Manager?',
                12=> 'Does each SOP have the version number, the author, the list of appendices, and the number of pages over the total number of pages?',
                13=> 'Are appendix attached to all SOPs?',
                14=> 'Were the changes brought to previous version mentioned in current version of SOPs and other controlled documents?',
                15=> 'Are there procedures in place for replacing revised SOPs or other controlled documents and ensuring that old SOPs or other controlled document are not available for use (removed from circulation)?',
            ]],
            'c' => ['letter' => 'C', 'title' => 'Bioassay Laboratory', 'sort' => 3, 'questions' => [
                1=>  'Is the bioassay laboratory secured from unauthorised access?',
                2=>  'Is the work area neat?',
                3=>  'Is the water under the tables clean and changed regularly?',
                4=>  'Are laboratory tools safely secured and stored when not in use?',
                5=>  'Are racks labelled accordingly providing detailed information and well arranged?',
                6=>  'Are insecticide product waste disposed separately from regular waste?',
                7=>  'Is the laboratory environmentally controlled?',
                8=>  'Is the laboratory temperature monitored?',
                9=>  'Are appropriate dress procedures followed?',
                10=> 'Are lab coats clean and well arranged?',
                11=> 'Are SOPs related to laboratory activities up to date, signed and available?',
            ]],
            'd' => ['letter' => 'D', 'title' => 'Biomolecular Room', 'sort' => 4, 'questions' => [
                1=> 'Is access to the biomolecular room limited?',
                2=> 'Is the biomolecular room clean and well organised?',
                3=> 'Is the biomolecular room environmentally controlled?',
                4=> 'Is the biomolecular room temperature monitored?',
                5=> 'Are the equipment in the biomolecular room clean?',
            ]],
            'e' => ['letter' => 'E', 'title' => 'Shaker-Bath room and LLIN Washing area', 'sort' => 5, 'questions' => [
                1=> 'Is the shaker-bath room clean and well organised?',
                2=> 'Is the shaker-bath room free from water spillage?',
                3=> 'Are the equipment sheets for all shaker-baths up to date and available?',
                4=> 'Is the LLIN washing area neat and free from water spillage?',
            ]],
            'f' => ['letter' => 'F', 'title' => 'Chemical & Potter tower Room', 'sort' => 6, 'questions' => [
                1=>  'Is there someone responsible for the management of the chemical room?',
                2=>  'Is there limited access to chemical storage room?',
                3=>  'Is the chemical storage room neat and organized?',
                4=>  'Are there separate areas for storage of test/control/reference items?',
                5=>  'Is the chemical storage room environmentally controlled?',
                6=>  'Is the chemical room temperature continuously monitored?',
                7=>  'Is the chemical room temperature range adequate for insecticide products?',
                8=>  'Is the storage area adequately ventilated?',
                9=>  'Is there a separate area for the mixing of test items e.g. fume hood?',
                10=> 'Is there a separate area for spraying insecticides on substrates?',
                11=> 'Is there an extraction fan in the potter tower room?',
                12=> 'Are Test/control/reference substances and dilutions properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions) to ensure proper identification of test items?',
                13=> 'Is the SOP for reception, registration and storage of materials followed?',
                14=> 'Is the reception of test/control/reference items documented?',
                15=> 'Are there records of MSDS and Chemical analysis certificates of test items?',
                16=> 'Are there records of test/control/reference items usage?',
                17=> 'Is there a procedure for disposal of test items?',
                18=> 'Is test item disposal documented?',
                19=> 'Is there a calendar to update list of chemical products and mosquito nets?',
            ]],
            'g' => ['letter' => 'G', 'title' => 'Safety (changing) room', 'sort' => 7, 'questions' => [
                1=> 'Is there someone responsible for the management of the safety (changing) room?',
                2=> 'Is the safety room separated from other sections?',
                3=> 'Is there limited access to the safety room?',
                4=> 'Is the locker for storage of facemask adequately locked?',
                5=> 'Are safety materials adequate for use?',
                6=> 'Is there a safety procedure for the Facility?',
                7=> 'Is there a calendar for safety inspections?',
            ]],
            'h' => ['letter' => 'H', 'title' => 'Storage and untreated block rooms', 'sort' => 8, 'questions' => [
                1=> 'Is there limited access to storage and untreated block rooms?',
                2=> 'Are the storage room and the untreated block room neat and organized?',
                3=> 'Is the untreated block room environmentally controlled?',
                4=> 'Is the untreated block room temperature continuously monitored?',
                5=> 'Are untreated blocks labelled for easy identification?',
                6=> 'Is there a separate area for the mixing of test items e.g. fume hood?',
            ]],
            'i' => ['letter' => 'I', 'title' => 'Net storage room and expired products Room', 'sort' => 9, 'questions' => [
                1=> 'Is there limited access to net storage room and the expired products room?',
                2=> 'Are the net and expired products storage rooms neat and organized?',
                3=> 'Is the net storage room environmentally controlled?',
                4=> 'Is the net room temperature continuously monitored?',
            ]],
            'j' => ['letter' => 'J', 'title' => 'Equipment', 'sort' => 10, 'questions' => [
                1=>  'Is there a person designated as responsible for equipment?',
                2=>  'Are equipment uniquely identified and included on the equipment inventory list?',
                3=>  'Is the equipment inventory up to date?',
                4=>  'Does each equipment have a file?',
                5=>  'Are Equipment instructions manual available and easily accessible?',
                6=>  'Are Equipment SOPs available and easily accessible and for each piece of equipment?',
                7=>  'Are calibration certificates available for each equipment?',
                8=>  'Are all equipment calibration certificate up to date?',
                9=>  'Is the Equipment calibration programme defined and regularly followed?',
                10=> 'Are Usage/maintenance/calibration/fault report sheets accessible and regularly filled in?',
                11=> 'Are Maintenance logs on equipment up-to-date?',
                12=> 'Is the equipment history regularly written in the log book?',
                13=> 'Are equipment cleaned after use?',
                14=> 'Do all equipment appear to be in good repair?',
                15=> 'Are equipment adequately stored when not used?',
            ]],
            'k' => ['letter' => 'K', 'title' => 'Staff Offices & Buildings', 'sort' => 11, 'questions' => [
                1=>  'Are different sections within the Facility clearly defined?',
                2=>  'Are all entry ways secured from unauthorized access?',
                3=>  'Are offices clean and well maintained?',
                4=>  'Is the entire building clean and well maintained on daily basis?',
                5=>  'Are all floors free of liquids to avoid slips and falls?',
                6=>  'Is there any housekeeping issues that need to be addressed?',
                7=>  'Are all plugs and cords in good condition?',
                8=>  'Are there electrical switches, switch plates or receptacles that are cracked, broken or have exposed contacts?',
                9=>  'Are all electrical circuit breakers identified?',
                10=> 'Is there any circuit breakers regularly tripping?',
                11=> 'Are surveillance video cameras working?',
                12=> 'Is the building equipped with fire extinguishers?',
                13=> 'Are there any security issues to be addressed?',
            ]],
            'l' => ['letter' => 'L', 'title' => 'Data Management', 'sort' => 12, 'questions' => [
                1=>  'Is there a personnel responsible for the development, validation, operation and maintenance of computerised systems?',
                2=>  'Is the data entry room secured from unauthorised access?',
                3=>  'Is the data entry room equipped with an extinguisher?',
                4=>  'Is the data entry room clean and well organised?',
                5=>  'Is the data entry room environmentally controlled and monitored?',
                6=>  'Is the temperature or humidity reported when out of range?',
                7=>  'Is there a documented policy for the recording and management of data?',
                8=>  'Are there SOPs for data management and are they all available?',
                9=>  'Are all data entry computers protected by a password system?',
                10=> 'Are computers regularly maintained?',
                11=> 'Is the maintenance of computerised systems used in GLP studies up to date?',
                12=> 'Are all computers equipped with an up-to-date anti-virus programme?',
                13=> 'Are peripheral components of computer hardware in good state?',
                14=> 'Is there records of any problems or fault detected and any remedial action taken during operation of the system?',
                15=> 'Are there computers taken out of the system?',
                16=> 'Is the server regularly maintained?',
                17=> 'Is there a backup system in place where data are secured?',
                18=> 'Is data recorded directly, legibly and indelibly?',
                19=> 'Are all data signed and dated at the time of entry?',
                20=> 'Are all data double entered?',
                21=> 'Are alterations to data such that they do not obscure the original and indicate the person making the alteration, the date of the alteration and the reason for the alteration using the appropriate error correction code where appropriate?',
                22=> 'Are computer systems used to generate study data?',
                23=> 'Are computerised systems regularly validated?',
                24=> 'Is the frequency for validation of computerised system defined?',
                25=> 'Are there any issues to be addressed?',
            ]],
            'm' => ['letter' => 'M', 'title' => 'Archive', 'sort' => 13, 'questions' => [
                1=>  'Is the archive room secured from unauthorised access?',
                2=>  'Is the archive room equipped with an extinguisher?',
                3=>  'Are the facilities secured and resistant to fire?',
                4=>  'Is the archive room neat and well organised?',
                5=>  'Is the archive room environmentally controlled?',
                6=>  'Is the archive room temperature monitored?',
                7=>  'Is there a designated archivist?',
                8=>  'Is there a deputy archivist?',
                9=>  'Are non GLP files separated from GLP files?',
                10=> 'Are all cabinets locked?',
                11=> 'Are there documented procedures for the submission of data to and the withdrawal of data from archive?',
                12=> 'Are there SOPs for activities performed in the archive and are they available?',
                13=> 'Is the material indexed to expedite retrieval?',
                14=> 'Is the Archive logbook regularly filled in?',
                15=> 'Is the archivist made aware of the contents of study files to be archived?',
                16=> 'Are completed studies project boxes followed up for archive?',
            ]],
            'n' => ['letter' => 'N', 'title' => 'Insectary and Annex', 'sort' => 14, 'questions' => [
                1=>  'Is there a designated personnel responsible for the management of the insectary and the animal house?',
                2=>  'Are copies of personnel file available and up to date?',
                3=>  'Is the insectary neat and well kept?',
                4=>  'Is access to the insectary rooms limited?',
                5=>  'Are surveillance video cameras in working order?',
                6=>  'Is the organogram available?',
                7=>  'Is the floor plan available?',
                8=>  'Is the insectary policy manual available?',
                9=>  'Are SOPs related to insectary activities available?',
                10=> 'Is there a calibration plan for insectary equipment?',
                11=> 'Are all equipment sheets regularly filled and signed by unit supervisor?',
                12=> 'Are insectary materials well arranged?',
                13=> 'Are appropriate dress procedures followed?',
                14=> 'Are insectary coats clean and well arranged?',
                15=> 'Is the water under the tables regularly changed and clean?',
                16=> 'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                17=> 'Are adult mosquito separated from larvae?',
                18=> 'Are adult mosquito rooms environmentally controlled and monitored?',
                19=> 'Are the mosquito cages labelled accordingly and well arranged?',
                20=> 'Are breeding cages separated from test cages?',
                21=> 'Is resistance test performed for each mosquito strain?',
                22=> 'Is the resistance status report provided to the FM and satisfactory?',
                23=> 'Are mosquito production sheets regularly filled?',
                24=> 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to?',
                25=> 'Are all sheets signed by the unit supervisor?',
            ]],
            'o' => ['letter' => 'O', 'title' => 'Animal House', 'sort' => 15, 'questions' => [
                1=>  'Is there a person responsible for the management of the animal house?',
                2=>  'Is the animal house neat?',
                3=>  'Is access to the animal house limited?',
                4=>  'Are the animal cages clearly labelled?',
                5=>  'Is each animal cage locked?',
                6=>  'Are breeding animal separated from test animal?',
                7=>  'Are animal house materials well arranged?',
                8=>  'Are appropriate dress procedures followed?',
                9=>  'Are SOPs related to animal house activities available?',
                10=> 'Are animal maintenance forms regularly filled and signed?',
                11=> 'Is there a documented procedure for releasing animals for test or blood feeding?',
                12=> 'Is the animal house regularly disinfected?',
            ]],
        ];

        $this->seedSectionsAndQuestions($tpl, $sections, 'yes_no_na');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Facility Inspection — Covè  (QA-PR-1-001B/06)  9 sections A–I
    // ─────────────────────────────────────────────────────────────────────────
    private function seedFacilityInspectionCove(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'facility_cove'], [
            'name'           => 'Facility Inspection Checklist (Field Site — Covè)',
            'reference_code' => 'QA-PR-1-001B/06',
            'version'        => '1.0',
            'category'       => 'facility',
        ]);

        $sections = [
            'a' => ['letter' => 'A', 'title' => 'General',                         'sort' => 1, 'questions' => [
                1 => 'Have all non-conformances raised from previous facility inspection been corrected?',
            ]],
            'b' => ['letter' => 'B', 'title' => 'Staff Offices & Buildings',        'sort' => 2, 'questions' => [
                1=>  'Are different sections within the Facility clearly defined?',
                2=>  'Are all entry ways secured from unauthorized access?',
                3=>  'Are offices clean and well maintained?',
                4=>  'Is the entire building clean and well maintained on daily basis?',
                5=>  'Are all floors free of liquids to avoid trips and falls?',
                6=>  'Is there any housekeeping issues that need to be addressed?',
                7=>  'Are all plugs and cords in good condition?',
                8=>  'Are there electrical switches, switch plates or receptacles that are cracked, broken or have exposed contacts?',
                9=>  'Are all electrical circuit breakers identified?',
                10=> 'Is there any circuit breakers regularly tripping?',
                11=> 'Are surveillance video cameras working?',
                12=> 'Is the building equipped with fire extinguishers?',
                13=> 'Are there any security issues to be addressed?',
                14=> 'Is the organizational chart up to date and available in every section?',
                15=> 'Is a floor plan up to date and available?',
                16=> 'Is there a copy of the field site staff file?',
                17=> 'Are SOPs related to field site activities up to date and available?',
                18=> 'Are all data entry computers protected by a code system?',
                19=> 'Are computers regularly maintained?',
                20=> 'Are all computers equipped with an up-to-date anti-virus programme?',
                21=> 'Is there a backup system in place where data are secured?',
                22=> 'Is data recorded directly, legibly and indelibly?',
                23=> 'Are all data signed and dated at the time of entry?',
                24=> 'Are softwares regularly validated?',
            ]],
            'c' => ['letter' => 'C', 'title' => 'Bioassay Laboratory Field site',  'sort' => 3, 'questions' => [
                1=>  'Is the bioassay laboratory secured from unauthorised access?',
                2=>  'Is the work area neat?',
                3=>  'Is the water under the tables clean and changed regularly?',
                4=>  'Are laboratory tools safely secured and stored when not in used?',
                5=>  'Are racks labelled accordingly providing detailed information and well arranged?',
                6=>  'Are appropriate dress procedures followed?',
                7=>  'Are insecticide product waste disposed separately from regular waste?',
                8=>  'Is the laboratory environmentally controlled?',
                9=>  'Is the laboratory temperature monitored?',
                10=> 'Are lab coats clean and well arranged?',
                11=> 'Is there a person designated as responsible for equipment, is this clearly defined and are designated individuals aware of their responsibilities?',
                12=> 'Are equipment cleaned after use?',
                13=> 'Are equipment uniquely identified and included on the equipment inventory list?',
                14=> 'Do Equipment appear to be in good repair?',
                15=> 'Are Equipment adequately stored when not used?',
                16=> 'Are Instructions manual easily accessible?',
                17=> 'Are Equipment SOP easily accessible and available and each piece of equipment?',
                18=> 'Are Usage/maintenance/calibration/fault report sheets accessible and regularly filled in?',
                19=> 'Is the Equipment calibration programme defined and regularly followed?',
                20=> 'Are Maintenance logs on equipment up-to-date?',
                21=> 'Are Equipment history regularly written in the log book?',
                22=> 'Are SOPs related to laboratory activities up to date, signed and available?',
            ]],
            'd' => ['letter' => 'D', 'title' => 'Chemical Room & Non-treated material Room', 'sort' => 4, 'questions' => [
                1=>  'Is there someone responsible for the management of the chemical room?',
                2=>  'Is there limited access to chemical storage room and non-treated material storage room?',
                3=>  'Is the Test/control/reference substance storage room neat and organized?',
                4=>  'Are there separate areas for storage of test/control/reference items?',
                5=>  'Is the chemical storage room environmentally controlled?',
                6=>  'Is the chemical room temperature continuously monitored?',
                7=>  'Is the chemical room temperature range adequate for insecticide products?',
                8=>  'Is the storage area adequately ventilated?',
                9=>  'Is there a separate area for the mixing of test items?',
                10=> 'Are test item dilutions labelled to ensure proper identification of test item?',
                11=> 'Is the SOP for reception, registration and storage of materials followed?',
                12=> 'Is the reception of test/control/reference items documented (test item reception book)?',
                13=> 'Are Test/control/reference substances properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions, MSDS)?',
                14=> 'Are there records of test/control/reference items usage?',
                15=> 'Is there a procedure for disposal of test items?',
                16=> 'Is test item disposal documented?',
            ]],
            'e' => ['letter' => 'E', 'title' => 'Experimental Huts – SITE 1',       'sort' => 5, 'questions' => [
                1=>  'Is the experimental hut site secured?',
                2=>  'Are the huts clean and well maintained?',
                3=>  'Are the gutters filled with clean water?',
                4=>  'Are there any cracks on the walls of the huts?',
                5=>  'Are all the huts locked?',
                6=>  'Is the security guard available?',
                7=>  'Are the hut surroundings clean and well maintained?',
                8=>  'Are the toilets clean and well maintained?',
                9=>  'Are the preparation rooms clean and well maintained?',
                10=> 'Is the temperature in the huts being recorded?',
                11=> 'Are the checklist being filled on a daily basis?',
                12=> 'Are the cows well maintained?',
                13=> 'Are there any issues to be addressed?',
            ]],
            'f' => ['letter' => 'F', 'title' => 'Experimental Huts – SITE 2',       'sort' => 6, 'questions' => [
                1=>  'Is the experimental hut site secured?',
                2=>  'Are the huts clean and well maintained?',
                3=>  'Are the gutters filled with clean water?',
                4=>  'Are there any cracks on the walls of the huts?',
                5=>  'Are all the huts locked?',
                6=>  'Is the security guard available?',
                7=>  'Are the hut surroundings clean and well maintained?',
                8=>  'Are the toilets clean and well maintained?',
                9=>  'Are the preparation rooms clean and well maintained?',
                10=> 'Is the temperature in the huts being recorded?',
                11=> 'Are the checklist being filled on a daily basis?',
                12=> 'Are the cows well maintained?',
                13=> 'Are there any issues to be addressed?',
            ]],
            'g' => ['letter' => 'G', 'title' => 'Experimental Huts – SITE 3',       'sort' => 7, 'questions' => [
                1=>  'Is the experimental hut site secured?',
                2=>  'Are the huts clean and well maintained?',
                3=>  'Are the gutters filled with clean water?',
                4=>  'Are there any cracks on the walls of the huts?',
                5=>  'Are all the huts locked?',
                6=>  'Is the security guard available?',
                7=>  'Are the hut surroundings clean and well maintained?',
                8=>  'Are the toilets clean and well maintained?',
                9=>  'Are the preparation rooms clean and well maintained?',
                10=> 'Is the temperature in the huts being recorded?',
                11=> 'Are the checklist being filled on a daily basis?',
                12=> 'Are the cows well maintained?',
                13=> 'Are there any issues to be addressed?',
            ]],
            'h' => ['letter' => 'H', 'title' => 'Insectary',                        'sort' => 8, 'questions' => [
                1=>  'Is there a designated personnel responsible for the management of the insectary and the animal house?',
                2=>  'Are copies of personnel file available and up to date?',
                3=>  'Is the insectary neat and well kept?',
                4=>  'Is access to the insectary rooms limited?',
                5=>  'Are surveillance video cameras in working order?',
                6=>  'Is the organogram available?',
                7=>  'Is the floor plan available?',
                8=>  'Is the insectary policy manual available?',
                9=>  'Are SOPs related to insectary activities available?',
                10=> 'Is there a calibration plan for insectary equipment?',
                11=> 'Are all equipment sheets regularly filled and signed by unit supervisor?',
                12=> 'Are insectary materials well arranged?',
                13=> 'Are appropriate dress procedures followed?',
                14=> 'Are insectary coats clean and well arranged?',
                15=> 'Is the water under the tables regularly changed and clean?',
                16=> 'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                17=> 'Are adult mosquito separated from larvae?',
                18=> 'Are adult mosquito rooms environmentally controlled and monitored?',
                19=> 'Are the mosquito cages labelled accordingly and well arranged?',
                20=> 'Are breeding cages separated from test cages?',
                21=> 'Is resistance test performed for each mosquito strain?',
                22=> 'Is the resistance status report provided to the FM and satisfactory?',
                23=> 'Are mosquito production sheets regularly filled?',
                24=> 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to?',
                25=> 'Are all sheets signed by the unit supervisor?',
            ]],
            'i' => ['letter' => 'I', 'title' => 'Animal House',                     'sort' => 9, 'questions' => [
                1=>  'Is there a person responsible for the management of the animal house?',
                2=>  'Is the animal house neat?',
                3=>  'Is access to the animal house limited?',
                4=>  'Are the animal cages clearly labelled?',
                5=>  'Is each animal cage locked?',
                6=>  'Are breeding animal separated from test animal?',
                7=>  'Are animal house materials well arranged?',
                8=>  'Are appropriate dress procedures followed?',
                9=>  'Are SOPs related to animal house activities available?',
                10=> 'Are animal maintenance forms regularly filled and signed?',
                11=> 'Is there a documented procedure for releasing animals for test or blood feeding?',
                12=> 'Is the animal house regularly disinfected?',
            ]],
        ];

        $this->seedSectionsAndQuestions($tpl, $sections, 'yes_no_na');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Process Inspection  (5 sections A–E)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedProcessInspection(): void
    {
        $tpl = ClTemplate::updateOrCreate(['code' => 'process_inspection'], [
            'name'     => 'Process Inspection Checklist',
            'version'  => '1.0',
            'category' => 'facility',
        ]);

        $sections = [
            'a' => ['letter' => 'A', 'title' => 'Equipment Reception, Installation and Management', 'sort' => 1, 'questions' => [
                1=>  'Is there a designated personnel for the management of AIRID equipment?',
                2=>  'Is a request made for the purchase of each type of equipment?',
                3=>  'At delivery, are equipment in conformity with requirements submitted before purchase?',
                4=>  'Are equipment calibrated by an external body before first use and annually or according to agreed calibration frequency?',
                5=>  'Are calibration certificates attached at delivery or before First use?',
                6=>  'Are equipment delivered with manufacturer\'s guide?',
                7=>  'Are equipment approved by the Facility Manager?',
                8=>  'Is an internal code attributed to equipment and equipment included in the inventory list? (Registration)',
                9=>  'Are equipment tested after installation?',
                10=> 'Is a file created for each type of equipment?',
                11=> 'Are equipment stored according to the manufacturer\'s guide?',
                12=> 'Are there SOPs written and available for type of equipment and in every section?',
                13=> 'Is an equipment inventory list maintained and up to date for the equipment?',
                14=> 'Are equipment use to calibrate other equipment (e.g. Master data logger) indicated on the equipment inventory list as \'Reference\'?',
                15=> 'Are staff trained on the use of equipment?',
                16=> 'Are equipment used for intended purpose?',
                17=> 'Are all equipment forms filled regularly and accordingly?',
                18=> 'Is maintenance done on each equipment and according to the equipment maintenance programme?',
                19=> 'Are Maintenance logs on equipment up-to-date?',
                20=> 'Is equipment history regularly written in the log book?',
                21=> 'Are all calibration certificates up to date?',
                22=> 'Are internal calibrations performed regularly by staff and according to the established internal calibration programme?',
                23=> 'Are GLP equipment identified as such?',
                24=> 'Are faulty or obsolete equipment taken out of the system and labelled \'Do not use\'?',
            ]],
            'b' => ['letter' => 'B', 'title' => 'Test Item Reception, Storage and Management', 'sort' => 2, 'questions' => [
                1=>  'Is there someone responsible for the reception, storage and management of AIRID test items?',
                2=>  'Is there a procedure for the reception, storage and management of test items?',
                3=>  'Is there an SOP for the reception, registration and storage of test items?',
                4=>  'Is the reception of test/control/reference items documented?',
                5=>  'Were the documents such as certificates of analysis, MSDS, Correspondence etc. attached to test item upon delivery?',
                6=>  'If documents are not attached to test item upon delivery, is there a procedure in place to ensure they are requested from supplier or sponsor and made available to the personnel in charge of the managing AIRID test items?',
                7=>  'Are all test items related documents kept in a folder for reference purpose?',
                8=>  'Are test items registered at reception (i.e. Test Item reception form filled and signed)',
                9=>  'Is an internal code (AIRID Chemical code) attributed to each test item during registration?',
                10=> 'Is an acknowledgment of receipt filled and signed by the SD?',
                11=> 'Is reception feedback done to supplier or sponsor (Acknowledgement of receipt sent?)',
                12=> 'Are Test/control/reference substances properly labelled (Name, CAS or code number, Batch number, Expiration date, Storage conditions, MSDS) during storage?',
                13=> 'Is the test item stored according to requirements stated at delivery?',
                14=> 'Are test item dilutions labelled to ensure proper identification of test item?',
                15=> 'Is the environmental condition of storage area monitored and recorded on daily basis?',
                16=> 'Are there records of test/control/reference items usage?',
                17=> 'Are there records of test items transport conditions?',
                18=> 'Is there a procedure for disposal of expired test items?',
                19=> 'Is test item disposal documented?',
                20=> 'Is access to AIRID Test items limited?',
            ]],
            'c' => ['letter' => 'C', 'title' => 'Test System Request, Production, Supply and Management', 'sort' => 3, 'questions' => [
                1=>  'Is there a designated person in charge of test system request, production, supply and management?',
                2=>  'Is there a procedure in place for the request, production, supply and management of AIRID Test system?',
                3=>  'Is there an SOP for the request, production, supply and management of test system?',
                4=>  'Is the production of test system done on a daily basis and are records of production kept?',
                5=>  'Are different mosquito strains separated from each other in order to avoid cross contamination?',
                6=>  'Are breeding cages separated from test cages?',
                7=>  'Are adult mosquitoes separated from larvae?',
                8=>  'Is the environmental condition of rearing areas being monitored and recorded?',
                9=>  'When environmental conditions are out of required range, is this reported using a minor or a major incident form?',
                10=> 'Is resistance test performed for each mosquito strain?',
                11=> 'Is the resistance status report provided to the FM and satisfactory?',
                12=> 'When test system is supplied to the insectary, is the reception date recorded?',
                13=> 'Is the source of the test system supplied stated on the mosquito reception sheet?',
                14=> 'Upon reception of test system at the insectary are information such as Date received, Species, Strain, Stage, Estimated Quantity and Code recorded?',
                15=> 'When test system is needed by other units of AIRID facility, is a test system request submitted to the insectary supervisor for supply of mosquitoes?',
                16=> 'Are mosquitoes supplied as requested or within an acceptable period?',
                17=> 'Is there a registry showing record of mosquito cages released by the insectary and does the record reflect code of cage, age of mosquitoes and name of person cages were released to.',
                18=> 'Are the material transfer sheets and chain of custody sheets filled in during operation between the insectary and other units?',
                19=> 'Are mosquitoes being controlled during transportation and are there records of this?',
                20=> 'Are all test item related sheets signed by the unit supervisor?',
                21=> 'Are breeding animal separated from test animal?',
                22=> 'Are SOPs related to animal house activities available?',
                23=> 'Are animals identified using an internal ID code, name, sex, date of birth, colour of fur, size, and physiological status?',
                24=> 'Are animal maintained on a daily basis and are records of maintenance activities kept?',
                25=> 'Are animal maintenance forms regularly filled and signed?',
                26=> 'Is there a documented procedure for releasing animals for test or blood feeding?',
                27=> 'When animals are needed for testing, is an animal request form submitted to staff in charge for supply of animals?',
                28=> 'Are animals released as requested?',
                29=> 'Is an animal release form filled during release of animals?',
                30=> 'In the case where animals are returned to the animal house, are there records of this?',
                31=> 'Are animals followed up individually and is the individual animal follow-up filled in and signed?',
            ]],
            'd' => ['letter' => 'D', 'title' => 'Computerized system Reception, registration, validation and maintenance', 'sort' => 4, 'questions' => [
                1=>  'Is there a designated person in charge of the AIRID computerized system?',
                2=>  'Is a user request form filled before the purchase of computerized system?',
                3=>  'Are purchase approved by the FM?',
                4=>  'Upon delivery, are documents such as computer\'s Manual, Guarantee and Characteristics form etc. attached to computerised system?',
                5=>  'In the case where these documents are not attached upon delivery, is there a system in place to ensure they are made available to the personnel in charge of computerized system?',
                6=>  'Is the user acceptance form filled and signed during reception?',
                7=>  'Is a risk assessment performed during reception?',
                8=>  'Is the computerized system validated and registered during reception?',
                9=>  'Is the computerised system configured after reception?',
                10=> 'Is there a folder for each computerized system?',
                11=> 'Are GLP computerized system clearly identified and separated from non-GLP?',
                12=> 'Is there a programme for the validation of softwares of computerized systems?',
                13=> 'Are validations performed according to established programme and are records of these kept?',
                14=> 'Are validations approved by the FM?',
                15=> 'Is there a programme for the maintenance of computerized system?',
                16=> 'Is maintenance performed following the established programme and is this recorded?',
                17=> 'Is the server regularly maintained?',
                18=> 'Are activities performed on computerized system recorded in the computer logbook?',
                19=> 'Are computers secured with a password system?',
                20=> 'Are data sent from other units verified to ensure they were not corrupted during transfer?',
                21=> 'Is the data verification checklist regularly filled and signed?',
                22=> 'Is there a backup system in place where data are secured?',
                23=> 'Is there an inventory list of AIRID computerized system and is the list up to date?',
                24=> 'Is there a procedure for the retrieval of data?',
                25=> 'Is there a procedure for the retrieval/disposal of computerized system?',
            ]],
            'e' => ['letter' => 'E', 'title' => 'Safety Procedures', 'sort' => 5, 'questions' => [
                1=>  'Is there a designated person in charge of the Safety?',
                2=>  'Is there a calendar for safety inspections?',
                3=>  'Is risk assessment performed before each Glp studies?',
                4=>  'Are PPE clothing put on during laboratory activities?',
                5=>  'Is decontamination regularly done before using any materials?',
                6=>  'Are fire extinguishers accessible and up to date yearly?',
                7=>  'Are first aid kits regularly checked and safety material to ensure they are intact?',
                8=>  'Are incidents reported effectively and submitted to safety officer?',
                9=>  'Are corrective actions implemented after incident reports?',
                10=> 'Are work related health issues reported in cases of occurrences?',
                11=> 'Is the Respirator Qualitative Face Fit test performed for each staff each year?',
            ]],
        ];

        $this->seedSectionsAndQuestions($tpl, $sections, 'yes_no_na');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Critical Phase Inspection Checklists  (13 types, A–M)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedCriticalPhaseChecklists(): void
    {
        $checklists = [
            'cone_llin' => [
                'name' => 'Cone Bioassay with LLIN samples', 'letter' => 'A',
                'questions' => [
                    1=>  'Was decontamination of laboratory tools performed?',
                    2=>  'Is the shaker bath calibrated at 155 rounds per minutes, at the temperature of 30°C during washing for 10 min?',
                    3=>  'Are mosquito net samples stored at 30°C in the incubator?',
                    4=>  'Are mosquito net sample stored according to protocol?',
                    5=>  'Is the test system (mosquitoes) available for test?',
                    6=>  'Is/ Are the mosquito cage(s) labelled accordingly?',
                    7=>  'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    8=>  'Are mosquitoes used for test of recommended age or according to protocol?',
                    9=>  'Is the mosquito strain used according to protocol?',
                    10=> 'Is the number of mosquitoes exposed according to protocol?',
                    11=> 'Is the time of contact respected?',
                    12=> 'Is the KD recorded exactly 1 hour after time of contact?',
                    13=> 'Are net samples stored at correct temperature and according to protocol after use?',
                ],
            ],
            'cone_irs_blocks_treatment' => [
                'name' => 'Cone Bioassay with IRS blocks (Blocks treatment)', 'letter' => 'B',
                'questions' => [
                    1=>  'Was the potter tower calibrated before impregnation?',
                    2=>  'Are the balances used calibrated?',
                    3=>  'Was the insecticide stored at the correct temperature?',
                    4=>  'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    5=>  'Was the fume hood used for mixture of insecticide dilutions?',
                    6=>  'Was the appropriate PPE (coat, mask, protection glasses etc.) followed?',
                    7=>  'Are the blocks used of same age?',
                    8=>  'Are the blocks used for spraying within the acceptable range of pH?',
                    9=>  'Are the blocks properly labelled?',
                    10=> 'Are the treated blocks stored at the correct temperature?',
                ],
            ],
            'cone_irs_blocks_test' => [
                'name' => 'Cone Bioassay with IRS blocks (Test)', 'letter' => 'C',
                'questions' => [
                    1=>  'Are all blocks used for testing within the acceptable ranges?',
                    2=>  'Are laboratory tools decontaminated before use?',
                    3=>  'Is the environmental condition of the laboratory monitored and recorded and according to protocol?',
                    4=>  'Is the number of mosquitoes exposed according to Protocol?',
                    5=>  'Is the correct mosquito strain being used?',
                    6=>  'Are the mosquitoes used of the age specified by the protocol?',
                    7=>  'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    8=>  'Is the time of contact respected?',
                    9=>  'Is the KD recorded exactly 1 hour after time of contact?',
                    10=> 'Is the correct data sheet used?',
                    11=> 'Are IRS blocks stored at correct temperature after use?',
                ],
            ],
            'tunnel_test' => [
                'name' => 'Tunnel Test', 'letter' => 'D',
                'questions' => [
                    1=>  'Are tunnels decontaminated before use?',
                    2=>  'Are laboratory tools decontaminated before use?',
                    3=>  'Where each netting pieces with 9 holes of 1 cm in diameter each?',
                    4=>  'Are mosquitoes used (strain and age) for test according to protocol?',
                    5=>  'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    6=>  'Is the correct animal used? According Protocol?',
                    7=>  'Are mosquitoes and animals inserted in the correct compartment of the tunnels?',
                    8=>  'Is test performed in recommended environmental conditions?',
                    9=>  'Is the number of mosquitoes exposed according to Protocol?',
                    10=> 'Are net samples stored at correct temperature after use?',
                    11=> 'Is the time of contact respected?',
                    12=> 'Is the correct data sheet used?',
                    13=> 'Is blood feeding and immediate mortality scored?',
                    14=> 'Are mosquitoes placed in appropriate holding cups and are holding cups labelled appropriately?',
                    15=> 'Are mosquitoes provided access to 10% glucose solution?',
                    16=> 'Are net samples used for tunnel tests wrapped and stored according to protocol?',
                ],
            ],
            'llin_washing' => [
                'name' => 'Evaluation of Whole LLIN in Experimental huts (Washing and Cutting of Whole Nets)', 'letter' => 'E',
                'questions' => [
                    1=>  'Are Whole LLIN clearly labelled?',
                    2=>  'Are Whole LLIN cut as described in the protocol?',
                    3=>  'Are net samples cut for laboratory bioassays and chemical analysis respectively?',
                    4=>  'Is the number of net samples cut from the whole LLINs according to protocol?',
                    5=>  'Are net samples cut wrapped and labelled appropriately?',
                    6=>  'Are net samples stored according to protocol?',
                    7=>  'Is the time for washing whole net respected?',
                    8=>  'Is the washing procedure given in the protocol respected?',
                    9=>  'Are whole LLIN allowed to dry in recommended conditions (in the shade)?',
                    10=> 'Are whole LLIN stored in recommended conditions?',
                ],
            ],
            'llin_exp_huts' => [
                'name' => 'Evaluation of Whole LLIN in Experimental huts', 'letter' => 'F',
                'questions' => [
                    1=>  'Is a signed copy of the protocol available at the field site?',
                    2=>  'Were the experimental huts refurbished before start of evaluation?',
                    3=>  'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    4=>  'Was the consent form signed by all the sleeper?',
                    5=>  'Was a sleeper\'s rotation plan established?',
                    6=>  'Was a mosquito net rotation plan established?',
                    7=>  'Are the curtains lifted up in all the experimental huts?',
                    8=>  'Were the slits opened in all the experimental huts?',
                    9=>  'Were the sleepers in experimental hut for 9 hours?',
                    10=> 'Was a surprise visit performed?',
                    11=> 'Are mosquitoes nets rotated according to protocol?',
                    12=> 'Are sleepers rotated every day?',
                    13=> 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    14=> 'Are the experimental huts cleaned before each round?',
                    15=> 'Were mosquitoes transported to laboratory in recommended conditions?',
                    16=> 'Were laboratory tools decontaminated?',
                    17=> 'Was the mortality recorded after 24 hours after and according to protocol?',
                    18=> 'Is the laboratory environmental condition monitored, recorded and according to protocol?',
                    19=> 'Were incidents (major or minor) reported accordingly?',
                    20=> 'Are all raw data sheets signed by the Study Director?',
                ],
            ],
            'irs_treatment' => [
                'name' => 'IRS Treatment application', 'letter' => 'G',
                'questions' => [
                    1=>  'Is a signed copy of the protocol available at the field site?',
                    2=>  'Were the experimental huts refurbished before start of evaluation?',
                    3=>  'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    4=>  'Was the sprayer cleaned and decontaminated?',
                    5=>  'Was the Hudson Sprayer calibrated before use?',
                    6=>  'Was an absorbent paper fixed on the window slits and at the edges of the floor closest to the wall to avoid contamination of entry points and to pick any run off of insecticide from the walls during spraying?',
                    7=>  'Were the hut walls pre-marked with swatts?',
                    8=>  'Were filter papers fixed on the walls?',
                    9=>  'Is the lance speed respected?',
                    10=> 'Was the correct PPE used?',
                    11=> 'Is the pressure checked and sprayer pumped back after spraying each wall or hut?',
                    12=> 'After spraying, was the spray tank depressurised and the volume of insecticide solution left in the tank measured and recorded?',
                    13=> 'Was the sprayer washed properly and according to SOP?',
                ],
            ],
            'irs_trial' => [
                'name' => 'IRS Trial', 'letter' => 'H',
                'questions' => [
                    1=>  'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    2=>  'Was the consent form signed by all the sleepers?',
                    3=>  'Was a sleeper\'s rotation plan established?',
                    4=>  'Are sleepers rotated every day?',
                    5=>  'Were the sleepers provided each with a torch, an aspirator, a broom, a container for urinating and labelled plastic cups?',
                    6=>  'Were the windows opened, the curtain lifted and the door closed?',
                    7=>  'Were the sleepers in experimental hut from 9pm and 6 am?',
                    8=>  'Were mosquitoes collected in the evening and in the morning?',
                    9=>  'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    10=> 'Were captured mosquitoes transported to laboratory in recommended conditions?',
                    11=> 'Was a surprise visit performed?',
                    12=> 'Are the experimental huts cleaned before each round?',
                    13=> 'Is the correct data sheet used?',
                    14=> 'Are mosquitoes preserved in recommended conditions for analysis?',
                ],
            ],
            'cone_irs_walls' => [
                'name' => 'Cone Bioassay on IRS treated walls', 'letter' => 'I',
                'questions' => [
                    1=>  'Was each wall numbered including the ceiling?',
                    2=>  'Were the mosquito holding cups labelled according to corresponding wall?',
                    3=>  'Are mosquitoes used for test according to protocol?',
                    4=>  'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    5=>  'Were testing tools decontaminated?',
                    6=>  'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    7=>  'Is the number of mosquitoes exposed according to Protocol?',
                    8=>  'Is the time of contact respected?',
                    9=>  'Was honey juice given to the mosquitoes?',
                    10=> 'Is the KD recorded exactly 1 hour after time of contact?',
                    11=> 'Is the correct data sheet used?',
                    12=> 'Were mosquitoes transported to laboratory in recommended conditions?',
                ],
            ],
            'cylinder_bioassay' => [
                'name' => 'Cylinder Bioassay', 'letter' => 'J',
                'questions' => [
                    1=> 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    2=> 'Are mosquitoes used for test according to protocol?',
                    3=> 'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    4=> 'Is the number of mosquitoes exposed according to Protocol?',
                    5=> 'Is the time of contact respected?',
                    6=> 'Is the KD recorded exactly 1 hour after time of contact?',
                    7=> 'Is the correct data sheet used?',
                    8=> 'Are mosquitoes preserved in proper conditions after testing?',
                ],
            ],
            'cdc_bottle_coating' => [
                'name' => 'CDC Bottle Bioassay (Coating)', 'letter' => 'K',
                'questions' => [
                    1=> 'Were CDC bottles washed before use?',
                    2=> 'Was the calculation sheet filled and signed by Study Director before mixture of insecticide?',
                    3=> 'Was the stock solution stored in the refrigerator at 4°C?',
                    4=> 'Were the CDC bottles labelled accordingly?',
                    5=> 'Was the stock solution, after being removed from the refrigerator, left at room temperature for 1–2 hours before use?',
                    6=> 'Were the CDC bottles allowed to rotate for 15 minutes on the tube roller?',
                    7=> 'Were the CDC bottles kept in a dark place after coating?',
                    8=> 'Were the CDC bottles allow to dry after coating?',
                ],
            ],
            'cdc_bottle_test' => [
                'name' => 'CDC Bottle Bioassay (Test)', 'letter' => 'L',
                'questions' => [
                    1=>  'Is testing done 24 hours after coating?',
                    2=>  'Is the correct mosquito strain being used?',
                    3=>  'Are mosquitoes of the age specified by protocol?',
                    4=>  'Were mosquitoes allowed to acclimatize with laboratory environment for 1 hour before testing?',
                    5=>  'Is the number of mosquitoes exposed according to Protocol?',
                    6=>  'Is the time of exposure according to protocol?',
                    7=>  'Is the time of contact respected?',
                    8=>  'Is the KD recorded according to protocol?',
                    9=>  'Is the correct data sheet used?',
                    10=> 'Is the delayed mortality recorded according to protocol?',
                ],
            ],
            'spatial_repellents' => [
                'name' => 'Evaluation of spatial repellents in Experimental huts', 'letter' => 'M',
                'questions' => [
                    1=>  'Is a signed copy of the protocol available at the field site?',
                    2=>  'Were the experimental huts refurbished before start of evaluation?',
                    3=>  'Were all the sleepers given an information note and trained on activities and confidentiality?',
                    4=>  'Was the consent form signed by all the sleeper?',
                    5=>  'Was a sleeper\'s rotation plan established?',
                    6=>  'Was a treatment rotation plan established?',
                    7=>  'Are the curtains lifted up in all the experimental huts?',
                    8=>  'Were the slits opened in all the experimental huts?',
                    9=>  'Were the sleepers in experimental hut for 9 hours?',
                    10=> 'Was a surprise visit performed?',
                    11=> 'Are treatments rotated according to protocol?',
                    12=> 'Are sleepers rotated every day?',
                    13=> 'Is the environmental condition of experimental huts monitored and recorded and according to protocol?',
                    14=> 'Are the experimental huts cleaned before each round?',
                    15=> 'Were mosquitoes transported to laboratory in recommended conditions?',
                    16=> 'Were laboratory tools decontaminated?',
                    17=> 'Was the mortality recorded after 24 hours after and according to protocol?',
                    18=> 'Is the laboratory environmental condition monitored, recorded and according to protocol?',
                    19=> 'Were incidents (major or minor) reported accordingly?',
                    20=> 'Are all raw data sheets signed by the Study Director?',
                ],
            ],
        ];

        $sort = 1;
        foreach ($checklists as $code => $def) {
            $tpl = ClTemplate::updateOrCreate(['code' => $code], [
                'name'     => $def['name'],
                'version'  => '1.0',
                'category' => 'critical_phase',
            ]);

            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => 'main'],
                ['letter' => $def['letter'], 'title' => $def['name'], 'sort_order' => 1, 'form_type' => 'yes_no_na']
            );

            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => 'yes_no_na', 'sort_order' => $num]
                );
            }
            $sort++;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSectionsAndQuestions(ClTemplate $tpl, array $sections, string $defaultResponseType): void
    {
        foreach ($sections as $code => $def) {
            $sec = ClSection::updateOrCreate(
                ['template_id' => $tpl->id, 'code' => $code],
                [
                    'letter'     => $def['letter'],
                    'title'      => $def['title'],
                    'sort_order' => $def['sort'],
                    'form_type'  => $def['type'] ?? $defaultResponseType,
                ]
            );

            foreach ($def['questions'] as $num => $text) {
                ClQuestion::updateOrCreate(
                    ['section_id' => $sec->id, 'item_number' => (string)$num],
                    ['text' => $text, 'response_type' => $def['type'] ?? $defaultResponseType, 'sort_order' => $num]
                );
            }
        }
    }
}
