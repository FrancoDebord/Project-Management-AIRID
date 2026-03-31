<?php

namespace App\Http\Controllers;

use App\Models\Pro_Personnel;
use App\Models\QaReviewInspection;
use App\Models\QaReviewResponse;
use App\Models\QaReviewCustomItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FmQaReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin,facility_manager']);
    }

    /** Build the personnel name list for autocomplete. */
    private function personnelNames(): array
    {
        return Pro_Personnel::orderBy('nom')
            ->get()
            ->map(fn($p) => trim(implode(' ', array_filter([
                $p->titre_personnel, $p->prenom, $p->nom
            ]))))
            ->filter()
            ->values()
            ->toArray();
    }

    /** List all QA Review Inspections. */
    public function index()
    {
        $reviews = QaReviewInspection::with('createdBy')
            ->orderByDesc('scheduled_date')
            ->get();

        $personnelNames = $this->personnelNames();

        return view('fm.qa-review.index', compact('reviews', 'personnelNames'));
    }

    /** Store a new scheduled review. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'scheduled_date' => 'required|date',
            'reviewer_name'  => 'nullable|string|max:200',
        ]);

        $review = QaReviewInspection::create([
            'scheduled_date' => $data['scheduled_date'],
            'reviewer_name'  => $data['reviewer_name'] ?? null,
            'status'         => 'scheduled',
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('fm.qa-review.show', $review->id)
            ->with('success', 'QA Review scheduled successfully.');
    }

    /** Show/fill the review form. */
    public function show(int $id)
    {
        $review = QaReviewInspection::with(['responses', 'customItems', 'createdBy'])
            ->findOrFail($id);

        $sections = QaReviewInspection::sections();

        // Key responses by "section_code.item_number"
        $responses = $review->responses->keyBy(fn($r) => $r->section_code . '.' . $r->item_number);

        $personnelNames = $this->personnelNames();

        return view('fm.qa-review.show', compact('review', 'sections', 'responses', 'personnelNames'));
    }

    /** Save responses via AJAX. */
    public function saveResponses(Request $request, int $id)
    {
        $review = QaReviewInspection::findOrFail($id);

        $request->validate([
            'responses'               => 'nullable|array',
            'responses.*.section_code'=> 'required|string',
            'responses.*.item_number' => 'required|integer|min:1',
            'responses.*.yes_no'      => 'nullable|in:yes,no',
            'responses.*.comments'    => 'nullable|string|max:2000',
            'responses.*.corrective_actions' => 'nullable|string|max:2000',
            'responses.*.ca_completed'       => 'nullable|boolean',
            'responses.*.ca_date'            => 'nullable|date',

            'custom_items'               => 'nullable|array',
            'custom_items.*.id'          => 'nullable|integer',
            'custom_items.*.question'    => 'required|string|max:1000',
            'custom_items.*.yes_no'      => 'nullable|in:yes,no',
            'custom_items.*.comments'    => 'nullable|string|max:2000',
            'custom_items.*.corrective_actions' => 'nullable|string|max:2000',
            'custom_items.*.ca_completed'        => 'nullable|boolean',

            'review_date'          => 'nullable|date',
            'reviewer_name'        => 'nullable|string|max:200',
            'date_signed'          => 'nullable|date',
            'meeting_date'         => 'nullable|date',
            'meeting_participants' => 'nullable|string|max:3000',
            'meeting_notes'        => 'nullable|string|max:5000',
            'status'               => 'nullable|in:scheduled,in_progress,completed',
        ]);

        DB::transaction(function () use ($request, $review) {
            // Save fixed responses
            foreach ($request->input('responses', []) as $resp) {
                QaReviewResponse::updateOrCreate(
                    [
                        'inspection_id' => $review->id,
                        'section_code'  => $resp['section_code'],
                        'item_number'   => $resp['item_number'],
                    ],
                    [
                        'yes_no'             => $resp['yes_no'] ?? null,
                        'comments'           => $resp['comments'] ?? null,
                        'corrective_actions' => $resp['corrective_actions'] ?? null,
                        'ca_completed'       => (bool) ($resp['ca_completed'] ?? false),
                        'ca_date'            => $resp['ca_date'] ?? null,
                    ]
                );
            }

            // Sync custom items
            $keptIds = [];
            foreach ($request->input('custom_items', []) as $i => $ci) {
                $item = isset($ci['id']) && $ci['id']
                    ? QaReviewCustomItem::find($ci['id'])
                    : new QaReviewCustomItem(['inspection_id' => $review->id]);

                if (!$item) continue;

                $item->fill([
                    'inspection_id'      => $review->id,
                    'sort_order'         => $i + 1,
                    'question'           => $ci['question'],
                    'yes_no'             => $ci['yes_no'] ?? null,
                    'comments'           => $ci['comments'] ?? null,
                    'corrective_actions' => $ci['corrective_actions'] ?? null,
                    'ca_completed'       => (bool) ($ci['ca_completed'] ?? false),
                ])->save();

                $keptIds[] = $item->id;
            }

            // Delete removed custom items
            QaReviewCustomItem::where('inspection_id', $review->id)
                ->when(!empty($keptIds), fn($q) => $q->whereNotIn('id', $keptIds))
                ->delete();

            // Update review header
            $review->update([
                'review_date'          => $request->review_date ?? $review->review_date,
                'reviewer_name'        => $request->reviewer_name ?? $review->reviewer_name,
                'date_signed'          => $request->date_signed ?? $review->date_signed,
                'meeting_date'         => $request->meeting_date ?? $review->meeting_date,
                'meeting_participants' => $request->meeting_participants ?? $review->meeting_participants,
                'meeting_notes'        => $request->meeting_notes ?? $review->meeting_notes,
                'status'               => $request->status ?? $review->status,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Saved successfully.']);
    }

    /** Mark review as completed. */
    public function complete(int $id)
    {
        $review = QaReviewInspection::findOrFail($id);
        $review->update(['status' => 'completed']);
        return response()->json(['success' => true]);
    }

    /** Printable version. */
    public function print(int $id)
    {
        $review = QaReviewInspection::with(['responses', 'customItems'])->findOrFail($id);
        $sections  = QaReviewInspection::sections();
        $responses = $review->responses->keyBy(fn($r) => $r->section_code . '.' . $r->item_number);
        $logoPath  = public_path('storage/assets/logo/airid.jpg');

        return view('fm.qa-review.print', compact('review', 'sections', 'responses', 'logoPath'));
    }

    /** Delete a review (only if scheduled). */
    public function destroy(int $id)
    {
        $review = QaReviewInspection::findOrFail($id);
        if ($review->status !== 'scheduled') {
            return response()->json(['success' => false, 'message' => 'Only scheduled reviews can be deleted.'], 422);
        }
        $review->delete();
        return response()->json(['success' => true]);
    }
}
