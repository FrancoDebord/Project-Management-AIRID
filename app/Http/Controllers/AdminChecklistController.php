<?php

namespace App\Http\Controllers;

use App\Models\ClQuestion;
use App\Models\ClSection;
use App\Models\ClTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    // ── Index — list all templates ────────────────────────────────────────
    public function index()
    {
        $templates = ClTemplate::withCount(['questions as total_questions' => fn($q) => $q])
            ->with('sections')
            ->orderByRaw("FIELD(category,'qa','facility','protocol','critical_phase')")
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('admin.checklists.index', compact('templates'));
    }

    // ── Show — one template with all its sections & questions ─────────────
    public function show(ClTemplate $template)
    {
        $template->load(['sections' => function ($q) {
            $q->orderBy('sort_order')->with(['questions' => fn($q2) => $q2->orderBy('sort_order')]);
        }]);

        return view('admin.checklists.show', compact('template'));
    }

    // ── Store — add a new question to a section ───────────────────────────
    public function storeQuestion(Request $request, ClSection $section): JsonResponse
    {
        $data = $request->validate([
            'item_number'   => 'required|string|max:20',
            'text'          => 'required|string',
            'response_type' => 'required|in:yes_no_na,yes_no,checkbox_date_text,text_only,staff_training,study_box_item',
            'notes'         => 'nullable|string',
        ]);

        // Check uniqueness within the section
        if (ClQuestion::where('section_id', $section->id)->where('item_number', $data['item_number'])->exists()) {
            return response()->json(['error' => 'Item number already exists in this section.'], 422);
        }

        $maxOrder = ClQuestion::where('section_id', $section->id)->max('sort_order') ?? 0;
        $q = ClQuestion::create(array_merge($data, [
            'section_id' => $section->id,
            'sort_order' => $maxOrder + 1,
        ]));

        return response()->json(['success' => true, 'question' => $q]);
    }

    // ── Update — edit an existing question ────────────────────────────────
    public function updateQuestion(Request $request, ClQuestion $question): JsonResponse
    {
        $data = $request->validate([
            'text'          => 'sometimes|required|string',
            'response_type' => 'sometimes|required|in:yes_no_na,yes_no,checkbox_date_text,text_only,staff_training,study_box_item',
            'notes'         => 'nullable|string',
            'sort_order'    => 'sometimes|integer',
        ]);

        // If question is used, only allow cosmetic text edits — no response_type change
        if ($question->usage_count > 0 && isset($data['response_type'])
            && $data['response_type'] !== $question->response_type) {
            return response()->json(['error' => 'Cannot change response type of a question already used in inspections.'], 422);
        }

        $question->update($data);

        return response()->json(['success' => true, 'question' => $question->fresh()]);
    }

    // ── Duplicate — copy a question (with copied_from_id trace) ──────────
    public function duplicateQuestion(ClQuestion $question): JsonResponse
    {
        $maxOrder = ClQuestion::where('section_id', $question->section_id)->max('sort_order') ?? 0;

        $copy = ClQuestion::create([
            'section_id'     => $question->section_id,
            'item_number'    => $question->item_number . '_copy',
            'text'           => $question->text . ' (copy)',
            'response_type'  => $question->response_type,
            'sort_order'     => $maxOrder + 1,
            'is_active'      => true,
            'copied_from_id' => $question->id,
            'notes'          => $question->notes,
        ]);

        return response()->json(['success' => true, 'question' => $copy]);
    }

    // ── Toggle active ─────────────────────────────────────────────────────
    public function toggleQuestion(ClQuestion $question): JsonResponse
    {
        $question->update(['is_active' => !$question->is_active]);
        return response()->json(['success' => true, 'is_active' => $question->is_active]);
    }

    // ── Delete — only if usage_count = 0 ─────────────────────────────────
    public function destroyQuestion(ClQuestion $question): JsonResponse
    {
        if (!$question->isDeletable()) {
            return response()->json(['error' => 'Cannot delete a question that has already been used in at least one inspection.'], 422);
        }

        $question->delete();
        return response()->json(['success' => true]);
    }
}
