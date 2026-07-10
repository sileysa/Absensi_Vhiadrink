<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stands = Stand::query()
            ->where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get()
            ->groupBy('region');

        $selectedStand = null;

        if ($request->session()->has('selected_stand_id')) {
            $selectedStand = Stand::query()
                ->where('is_active', true)
                ->find($request->session()->get('selected_stand_id'));
        }

        return view('dashboard.index', [
            'stands' => $stands,
            'selectedStand' => $selectedStand,
        ]);
    }

    public function selectStand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
        ]);

        $stand = Stand::query()
            ->where('is_active', true)
            ->findOrFail($validated['stand_id']);

        $request->session()->put('selected_stand_id', $stand->id);

        return redirect()
            ->route('dashboard')
            ->with('success', "Stand {$stand->name} ({$stand->region}) berhasil dipilih.");
    }

    public function clearStand(Request $request): RedirectResponse
    {
        $request->session()->forget('selected_stand_id');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pilihan stand telah direset.');
    }
}
