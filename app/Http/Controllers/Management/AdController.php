<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        return view('management.ads.index', compact('ads'));
    }

    public function create()
    {
        $positions = Ad::POSITIONS;
        $ad = new Ad();
        return view('management.ads.add_edit', compact('positions', 'ad'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|in:' . implode(',', array_keys(Ad::POSITIONS)),
            'content' => 'required',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        // Default order is 0
        if (!isset($validated['order'])) {
            $validated['order'] = 0;
        }

        Ad::create($validated);
        return redirect()->route('management.ads.index')->with('success', 'Ad created');
    }

    public function edit(Ad $ad)
    {
        $positions = Ad::POSITIONS;
        return view('management.ads.add_edit', compact('ad', 'positions'));
    }

    public function update(Request $request, Ad $ad)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|in:' . implode(',', array_keys(Ad::POSITIONS)),
            'content' => 'required',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        // Default order is 0
        if (!isset($validated['order'])) {
            $validated['order'] = 0;
        }

        $ad->update($validated);
        return redirect()->route('management.ads.index')->with('success', 'Ad updated');
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('management.ads.index')->with('success', 'Ad deleted');
    }

    public function reorder(Request $request)
    {
        $items = $request->validate([
            '*.id' => 'required|exists:ads,id',
            '*.order' => 'required|integer|min:0',
            '*.position' => 'required|string'
        ]);

        foreach ($items as $item) {
            Ad::where('id', $item['id'])->update([
                'order' => $item['order'],
                'position' => $item['position']
            ]);
        }

        return response()->json(['success' => true]);
    }
}