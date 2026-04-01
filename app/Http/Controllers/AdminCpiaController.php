<?php

namespace App\Http\Controllers;

use App\Models\CpiaItem;
use App\Models\CpiaSection;
use Illuminate\Http\Request;

class AdminCpiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super_admin');
    }

    /** List all sections with item counts */
    public function index()
    {
        $sections = CpiaSection::withCount('items')
            ->orderBy('sort_order')
            ->get();

        return view('admin.cpia.index', compact('sections'));
    }

    /** Show one section with all its items */
    public function show(CpiaSection $section)
    {
        $section->load('items');
        return view('admin.cpia.show', compact('section'));
    }

    /** Store a new item in a section */
    public function storeItem(Request $request, CpiaSection $section)
    {
        $data = $request->validate([
            'text'       => 'required|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $nextNum = ($section->items()->max('item_number') ?? 0) + 1;

        $section->items()->create([
            'item_number' => $nextNum,
            'text'        => $data['text'],
            'sort_order'  => $data['sort_order'] ?? $nextNum,
            'is_active'   => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Item added.']);
    }

    /** Update an existing item */
    public function updateItem(Request $request, CpiaItem $item)
    {
        $data = $request->validate([
            'text'       => 'required|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (!$item->isFullyEditable()) {
            // Allow text edit but not item_number change
            $item->update(['text' => $data['text']]);
        } else {
            $item->update([
                'text'       => $data['text'],
                'sort_order' => $data['sort_order'] ?? $item->sort_order,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Item updated.']);
    }

    /** Duplicate an item (copy with _copy suffix) */
    public function duplicateItem(CpiaItem $item)
    {
        $nextNum = ($item->section->items()->max('item_number') ?? 0) + 1;

        $copy = CpiaItem::create([
            'section_id'     => $item->section_id,
            'item_number'    => $nextNum,
            'text'           => $item->text . ' (copy)',
            'sort_order'     => $nextNum,
            'is_active'      => true,
            'copied_from_id' => $item->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Item duplicated.', 'item' => $copy]);
    }

    /** Toggle active/inactive */
    public function toggleItem(CpiaItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);
        return response()->json(['success' => true, 'is_active' => $item->is_active]);
    }

    /** Delete item (only if never used) */
    public function destroyItem(CpiaItem $item)
    {
        if (!$item->isDeletable()) {
            return response()->json(['success' => false, 'message' => 'This item has been used in assessments and cannot be deleted.'], 409);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted.']);
    }
}
