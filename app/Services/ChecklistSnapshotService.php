<?php

namespace App\Services;

use App\Models\ClTemplate;
use App\Models\InspectionResponse;
use App\Models\InspectionSnapshot;
use App\Models\Pro_QaInspection;
use Illuminate\Support\Facades\DB;

class ChecklistSnapshotService
{
    // ─────────────────────────────────────────────────────────────────────────
    // Template code resolution
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Maps an inspection to the cl_templates.code that defines its questions.
     * Returns null if the inspection type is not yet managed via the snapshot system.
     */
    public static function templateCode(Pro_QaInspection $inspection): ?string
    {
        $type = $inspection->type_inspection;
        $loc  = $inspection->facility_location;
        $slug = $inspection->checklist_slug;

        if ($type === 'Facility Inspection') {
            return $loc === 'cove' ? 'facility_cove' : 'facility_main';
        }
        if ($type === 'Process Inspection')  return 'process_inspection';
        if ($type === 'Study Protocol Inspection') return 'study_protocol';
        if ($type === 'Study Report Inspection')   return 'study_report';
        if ($type === 'Data Quality Inspection')   return 'data_quality';
        if (in_array($type, ['Study Protocol Amendment/Deviation Inspection',
                              'Study Report Amendment Inspection'])) {
            return 'amendment_deviation';
        }
        if ($type === 'Critical Phase Inspection' && $slug) {
            return str_replace('-', '_', $slug); // 'cone-llin' → 'cone_llin'
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // URL slug helpers
    // ─────────────────────────────────────────────────────────────────────────

    public static function urlSlug(string $templateCode, string $sectionCode): string
    {
        if ($sectionCode === 'main') {
            return match($templateCode) {
                'amendment_deviation' => 'amendment-deviation',
                default               => str_replace('_', '-', $templateCode),
            };
        }
        return match($templateCode) {
            'facility_main'      => 'facility-' . $sectionCode,
            'facility_cove'      => 'cove-' . $sectionCode,
            'process_inspection' => 'process-' . $sectionCode,
            default              => $sectionCode, // sp-a, sr-a, dq-a, I, II, …
        };
    }

    /**
     * Extract the "section key" — the short letter used inside HTML field names.
     * 'a' → 'a', 'sp-a' → 'a', 'dq-c' → 'c', 'main' → '', 'I' → 'I'
     */
    public static function sectionKey(string $sectionCode): string
    {
        if ($sectionCode === 'main') return '';
        $parts = explode('-', $sectionCode);
        return end($parts);
    }

    /**
     * HTML field-name prefix for standard (non-DQ) yes/no/na questions.
     * Returns 'a_' for section A, '' for single-section forms.
     */
    public static function fieldPrefix(string $templateCode, string $sectionCode, string $formType): string
    {
        // DQ forms use dedicated input name schemes (q_, v1_q_, q__response …)
        if (in_array($formType, ['dq_standard', 'dual_verification', 'study_box'])) {
            return '';
        }
        $key = self::sectionKey($sectionCode);
        return $key !== '' ? "{$key}_" : '';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Snapshot creation
    // ─────────────────────────────────────────────────────────────────────────

    public static function createSnapshot(Pro_QaInspection $inspection): void
    {
        $templateCode = self::templateCode($inspection);
        if (!$templateCode) return;

        $template = ClTemplate::where('code', $templateCode)
            ->with(['sections' => fn($q) => $q->orderBy('sort_order')
                ->with(['questions' => fn($q2) => $q2->where('is_active', true)->orderBy('sort_order')])
            ])->first();

        if (!$template) return;

        DB::transaction(function () use ($inspection, $template, $templateCode) {
            // Wipe any existing snapshots (allows re-snapshotting before any form is filled)
            InspectionSnapshot::where('inspection_id', $inspection->id)->delete();

            foreach ($template->sections as $section) {
                $sectionKey = self::sectionKey($section->code);
                $urlSlug    = self::urlSlug($templateCode, $section->code);

                foreach ($section->questions as $q) {
                    InspectionSnapshot::create([
                        'inspection_id'        => $inspection->id,
                        'cl_question_id'       => $q->id,
                        'template_code'        => $templateCode,
                        'section_code'         => $section->code,
                        'section_key'          => $sectionKey,
                        'section_letter'       => $section->letter,
                        'section_title'        => $section->title,
                        'section_subtitle'     => $section->subtitle,
                        'section_display_style'=> $section->display_style ?? 'normal',
                        'section_form_type'    => $section->form_type,
                        'section_sort_order'   => $section->sort_order,
                        'url_slug'             => $urlSlug,
                        'item_number'          => $q->item_number,
                        'text'                 => $q->text,
                        'response_type'        => $q->response_type,
                        'sort_order'           => $q->sort_order,
                    ]);

                    // Track usage on the master question
                    $q->increment('usage_count');
                    if (!$q->first_used_at) {
                        $q->update(['first_used_at' => now()]);
                    }
                }
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Building the $form array from snapshot
    // ─────────────────────────────────────────────────────────────────────────

    public static function buildForm(
        Pro_QaInspection $inspection,
        string $urlSlug
    ): ?array {
        $snapshots = InspectionSnapshot::where('inspection_id', $inspection->id)
            ->where('url_slug', $urlSlug)
            ->orderBy('sort_order')
            ->get();

        if ($snapshots->isEmpty()) return null;

        $first     = $snapshots->first();
        $formType  = $first->section_form_type ?? 'yes_no_na';
        $questions = [];

        foreach ($snapshots as $s) {
            $questions[$s->item_number] = $s->text;
        }

        return [
            'section'        => $first->section_key ?: $first->section_code,
            'section_code'   => $first->section_code,
            'url_slug'       => $urlSlug,
            'letter'         => $first->section_letter,
            'title'          => $first->section_title,
            'subtitle'       => $first->section_subtitle ?? null,
            'display_style'  => $first->section_display_style ?? 'normal',
            'form_type'      => $formType,
            'questions'      => $questions,
            'model'          => null,
            // Keep staff_count/type stubs so form.blade.php helpers don't break
            'type'           => $formType === 'staff_training' ? 'study_personnel' : null,
            'staff_count'    => 15,
        ];
    }

    /**
     * Returns all distinct sections for an inspection as an array of form arrays,
     * keyed by url_slug.
     */
    public static function allForms(Pro_QaInspection $inspection): array
    {
        $sections = InspectionSnapshot::where('inspection_id', $inspection->id)
            ->select('url_slug', 'section_code', 'section_key', 'section_letter',
                     'section_title', 'section_form_type', 'section_sort_order')
            ->distinct()
            ->orderBy('section_sort_order')
            ->get();

        // Count questions per url_slug for the index card display
        $questionCounts = InspectionSnapshot::where('inspection_id', $inspection->id)
            ->selectRaw('url_slug, count(*) as cnt')
            ->groupBy('url_slug')
            ->pluck('cnt', 'url_slug')
            ->toArray();

        $forms = [];
        foreach ($sections as $sec) {
            $qCount = $questionCounts[$sec->url_slug] ?? 0;
            $forms[$sec->url_slug] = [
                'section'    => $sec->section_key ?: $sec->section_code,
                'section_code' => $sec->section_code,
                'url_slug'   => $sec->url_slug,
                'letter'     => $sec->section_letter,
                'title'      => $sec->section_title,
                'form_type'  => $sec->section_form_type ?? 'yes_no_na',
                'model'      => null,
                'questions'  => array_fill(1, $qCount, ''), // stub array so count() works in views
            ];
        }
        return $forms;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Building the response proxy and DQ answer arrays from saved responses
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns [$record (proxy), $dqAnswers, $dqV1Answers, $dqV2Answers].
     * $record mimics the old Eloquent $record interface expected by form.blade.php.
     */
    public static function buildRecord(
        int    $inspectionId,
        string $sectionCode,
        string $formType,
        string $fieldPrefix,
        array  $allForms = []  // for deriving sections_done
    ): array {
        $responses = InspectionResponse::where('inspection_id', $inspectionId)
            ->where('section_code', $sectionCode)
            ->get()
            ->keyBy('item_number');

        // sections_done = all sections that have a saved _meta row
        $sectionsDone = InspectionResponse::where('inspection_id', $inspectionId)
            ->where('item_number', '_meta')
            ->pluck('section_code')
            ->toArray();

        $data = ['sections_done' => $sectionsDone];

        // Populate from _meta row
        $meta = $responses->get('_meta');
        if ($meta) {
            $data['comments']      = $meta->comments;
            $data['is_conforming'] = $meta->is_conforming;

            // Flatten extra_data into proxy (amendment fields, DQ header, staff, …)
            foreach ((array)($meta->extra_data ?? []) as $k => $v) {
                if ($k === 'staff' && is_array($v)) {
                    // Staff training: expand staff[1] → f_staff_1_result, etc.
                    foreach ($v as $i => $staffRow) {
                        $data["f_staff_{$i}_result"]  = $staffRow['result']  ?? null;
                        $data["f_staff_{$i}_level"]   = $staffRow['level']   ?? null;
                        $data["f_staff_{$i}_remarks"] = $staffRow['remarks'] ?? null;
                    }
                } else {
                    $data[$k] = $v;
                }
            }

            // DQ-specific section-prefixed fields (a_comments, a_is_conforming, etc.)
            $sectionKey = InspectionSnapshot::where('inspection_id', $inspectionId)
                ->where('section_code', $sectionCode)
                ->value('section_key') ?? '';
            if ($sectionKey) {
                $data["{$sectionKey}_comments"]      = $meta->comments;
                $data["{$sectionKey}_is_conforming"] = $meta->is_conforming;

                // DQ date/personnel from extra_data
                foreach (['date_performed','qa_personnel_id',
                          'v1_date_performed','v1_qa_personnel_id',
                          'v2_date_performed','v2_qa_personnel_id'] as $field) {
                    $val = $meta->extra_data[$field] ?? null;
                    $data["{$sectionKey}_{$field}"] = $val;
                }
            }
        }

        // DQ answer arrays
        $dqAnswers   = [];
        $dqV1Answers = [];
        $dqV2Answers = [];

        foreach ($responses as $itemNumber => $resp) {
            if ($itemNumber === '_meta') continue;

            if ($formType === 'dq_standard') {
                $dqAnswers[$itemNumber] = $resp->yes_no_na;

            } elseif ($formType === 'dual_verification') {
                $extra = (array)($resp->extra_data ?? []);
                $dqV1Answers[$itemNumber] = $extra['v1'] ?? null;
                $dqV2Answers[$itemNumber] = $extra['v2'] ?? null;

            } elseif ($formType === 'study_box') {
                $extra = (array)($resp->extra_data ?? []);
                $dqAnswers[$itemNumber] = $extra; // view does is_array($entry) check

            } else {
                // Standard yes/no/na — expose as {prefix}q{n}
                $fieldName = $fieldPrefix !== ''
                    ? "{$fieldPrefix}q{$itemNumber}"
                    : "q{$itemNumber}";
                $data[$fieldName] = $resp->yes_no_na;

                // checkbox_date_text (QA Activities)
                if ($formType === 'checkbox_date_text') {
                    $data["is_checked_{$itemNumber}"] = $resp->is_checked;
                    $data["date_performed_{$itemNumber}"] = $resp->date_value?->format('Y-m-d');
                    $data["means_of_verification_{$itemNumber}"] = $resp->text_response;
                }
            }
        }

        return [
            new InspectionResponseProxy($data),
            $dqAnswers,
            $dqV1Answers,
            $dqV2Answers,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Saving responses from POST
    // ─────────────────────────────────────────────────────────────────────────

    public static function saveSection(
        \Illuminate\Http\Request $request,
        Pro_QaInspection $inspection,
        string $urlSlug
    ): void {
        $snapshots = InspectionSnapshot::where('inspection_id', $inspection->id)
            ->where('url_slug', $urlSlug)
            ->orderBy('sort_order')
            ->get();

        if ($snapshots->isEmpty()) return;

        $first      = $snapshots->first();
        $sectionCode = $first->section_code;
        $sectionKey  = $first->section_key;  // 'a','b', '' for 'main'
        $formType    = $first->section_form_type ?? 'yes_no_na';
        $fp          = $sectionKey !== '' ? "{$sectionKey}_" : ''; // field prefix

        DB::transaction(function () use (
            $request, $inspection, $snapshots, $sectionCode, $sectionKey, $formType, $fp
        ) {
            // Delete old question-level responses for this section
            InspectionResponse::where('inspection_id', $inspection->id)
                ->where('section_code', $sectionCode)
                ->where('item_number', '!=', '_meta')
                ->delete();

            // Save per-question responses
            foreach ($snapshots as $snap) {
                $n = $snap->item_number;
                $rowData = [
                    'inspection_id' => $inspection->id,
                    'snapshot_id'   => $snap->id,
                    'section_code'  => $sectionCode,
                    'item_number'   => $n,
                    'created_by'    => auth()->id(),
                ];

                if ($formType === 'dq_standard') {
                    $rowData['yes_no_na'] = $request->input("q_{$n}") ?: null;

                } elseif ($formType === 'dual_verification') {
                    $rowData['extra_data'] = [
                        'v1' => $request->input("v1_q_{$n}") ?: null,
                        'v2' => $request->input("v2_q_{$n}") ?: null,
                    ];

                } elseif ($formType === 'study_box') {
                    $rowData['extra_data'] = [
                        'response' => $request->input("q_{$n}_response") ?: null,
                        'signed'   => $request->input("q_{$n}_signed")   ?: null,
                    ];

                } elseif ($formType === 'checkbox_date_text') {
                    $rowData['is_checked']    = (bool)$request->input("is_checked_{$n}");
                    $rowData['date_value']    = $request->input("date_performed_{$n}") ?: null;
                    $rowData['text_response'] = $request->input("means_of_verification_{$n}") ?: null;

                } else {
                    // Standard yes/no/na and yes/no forms
                    $rowData['yes_no_na'] = $request->input("{$fp}q{$n}") ?: null;
                }

                InspectionResponse::create($rowData);
            }

            // ── Build extra_data for the _meta row ────────────────────────
            $extraMeta = [];

            // DQ section A header
            if ($formType === 'dq_standard' && $sectionKey === 'a') {
                $extraMeta['aspects_inspected']   = $request->input('aspects_inspected', []);
                $extraMeta['study_start_date']    = $request->input('study_start_date') ?: null;
                $extraMeta['study_end_date']      = $request->input('study_end_date') ?: null;
                $extraMeta['study_director_name'] = $request->input('study_director_name');
                $extraMeta['qa_inspector_phone']  = $request->input('qa_inspector_phone');
                $extraMeta['qa_inspector_email']  = $request->input('qa_inspector_email');
                $extraMeta['personnel_involved']  = $request->input('personnel_involved', []);
            }

            // DQ date/personnel fields
            if (in_array($formType, ['dq_standard', 'study_box'])) {
                $extraMeta['date_performed']   = $request->input("{$sectionKey}_date_performed") ?: null;
                $extraMeta['qa_personnel_id']  = $request->input("{$sectionKey}_qa_personnel_id") ?: null;
            } elseif ($formType === 'dual_verification') {
                $extraMeta['v1_date_performed']   = $request->input("{$sectionKey}_v1_date_performed") ?: null;
                $extraMeta['v1_qa_personnel_id']  = $request->input("{$sectionKey}_v1_qa_personnel_id") ?: null;
                $extraMeta['v2_date_performed']   = $request->input("{$sectionKey}_v2_date_performed") ?: null;
                $extraMeta['v2_qa_personnel_id']  = $request->input("{$sectionKey}_v2_qa_personnel_id") ?: null;
            }

            // Amendment/Deviation header fields
            if ($sectionCode === 'main' && str_contains((string)$snapshots->first()?->template_code, 'amendment')) {
                $extraMeta['document_type']    = $request->input('document_type');
                $extraMeta['deviation_number'] = $request->input('deviation_number');
                $extraMeta['amendment_number'] = $request->input('amendment_number');
            }

            // Study Protocol section F — staff training (dynamic count from submitted keys)
            if ($formType === 'staff_training') {
                $staff = [];
                foreach ($request->all() as $key => $value) {
                    if (preg_match('/^f_staff_(\d+)_result$/', $key, $m)) {
                        $n = (int)$m[1];
                        $staff[$n] = [
                            'result'  => $value ?: null,
                            'level'   => $request->input("f_staff_{$n}_level")   ?: null,
                            'remarks' => $request->input("f_staff_{$n}_remarks") ?: null,
                        ];
                    }
                }
                $extraMeta['staff'] = $staff;
            }

            $isConforming = match($request->input('is_conforming')) {
                '1'  => true,
                '0'  => false,
                default => null,
            };

            InspectionResponse::updateOrCreate(
                [
                    'inspection_id' => $inspection->id,
                    'section_code'  => $sectionCode,
                    'item_number'   => '_meta',
                ],
                [
                    'snapshot_id'   => $snapshots->first()->id,
                    'comments'      => $request->input($fp !== '' ? "{$fp}comments" : 'comments'),
                    'is_conforming' => $isConforming,
                    'extra_data'    => !empty($extraMeta) ? $extraMeta : null,
                    'created_by'    => auth()->id(),
                ]
            );
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Section progress helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** Returns url_slugs of sections that have a saved _meta row. */
    public static function doneSlugs(int $inspectionId): array
    {
        $doneSectionCodes = InspectionResponse::where('inspection_id', $inspectionId)
            ->where('item_number', '_meta')
            ->pluck('section_code')
            ->toArray();

        return InspectionSnapshot::where('inspection_id', $inspectionId)
            ->whereIn('section_code', $doneSectionCodes)
            ->pluck('url_slug')
            ->unique()
            ->values()
            ->toArray();
    }

    /** Returns conformity keyed by url_slug. */
    public static function conformities(int $inspectionId): array
    {
        $rows = InspectionResponse::where('inspection_id', $inspectionId)
            ->where('item_number', '_meta')
            ->get();

        $codeToSlug = InspectionSnapshot::where('inspection_id', $inspectionId)
            ->pluck('url_slug', 'section_code')
            ->toArray();

        $result = [];
        foreach ($rows as $r) {
            $slug = $codeToSlug[$r->section_code] ?? null;
            if ($slug) $result[$slug] = $r->is_conforming;
        }
        return $result;
    }
}
