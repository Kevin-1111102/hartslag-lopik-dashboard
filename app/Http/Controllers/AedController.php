<?php

namespace App\Http\Controllers;

use App\Models\Aed;
use App\Models\Controle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AedController extends Controller
{
    /**
     * Display a listing of the user's AEDs.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $aeds = auth()->user()->aeds()
            ->withTrashed()
            ->when($search, function ($query) use ($search) {
                $query->where('serienummer', 'like', "%{$search}%")
                      ->orWhere('adres', 'like', "%{$search}%")
                      ->orWhere('plaats', 'like', "%{$search}%");
            })
            ->withCount('controles')
            ->needsAttention()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('aeds.index', compact('aeds', 'search'));
    }

    /**
     * Show the form for creating a new AED.
     */
    public function create(): View
    {
        return view('aeds.create');
    }

    /**
     * Store a newly created AED in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'serienummer' => 'nullable|string|max:255|unique:aeds,serienummer',
            'adres' => 'required|string|max:255',
            'huisnummer' => 'nullable|string|max:10',
            'plaats' => 'required|string|max:255',
            'beschrijving' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'onderhoudscode' => 'nullable|string|max:255',
            'batterij_vervaldatum' => 'nullable|date',
            'elektroden_vervaldatum' => 'nullable|date',
            'shl_beheerder' => 'boolean',
            'shl_verantwoordelijk_controle' => 'boolean',
            'shl_hartslagnu_beheer' => 'boolean',
            'externe_onderhoud' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'actief';

        Aed::create($validated);

        return redirect()->route('aeds.index')->with('success', 'AED toegevoegd uit roulatie!');
    }

    /**
     * Display the specified AED with history.
     */
    public function show(Aed $aed): View
    {
        if ($aed->user_id !== auth()->id()) {
            abort(403);
        }

        $controles = $aed->controles()->with('user')->latest()->get();

        return view('aeds.show', compact('aed', 'controles'));
    }

    /**
     * Show the form for editing the specified AED.
     */
    public function edit(Aed $aed): View
    {
        if ($aed->user_id !== auth()->id() || $aed->status === 'verwijderd') {
            abort(403);
        }

        return view('aeds.edit', compact('aed'));
    }

    /**
     * Update the specified AED in database.
     */
    public function update(Request $request, Aed $aed): RedirectResponse
    {
        if ($aed->user_id !== auth()->id() || $aed->status === 'verwijderd') {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'serienummer' => ['nullable', 'string', 'max:255', Rule::unique('aeds', 'serienummer')->ignore($aed->id)],
            'adres' => 'required|string|max:255',
            'huisnummer' => 'nullable|string|max:10',
            'plaats' => 'required|string|max:255',
            'beschrijving' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'onderhoudscode' => 'nullable|string|max:255',
            'batterij_vervaldatum' => 'nullable|date',
            'elektroden_vervaldatum' => 'nullable|date',
            'shl_beheerder' => 'boolean',
            'shl_verantwoordelijk_controle' => 'boolean',
            'shl_hartslagnu_beheer' => 'boolean',
            'externe_onderhoud' => 'boolean',
        ]);

        $aed->update($validated);

        return redirect()->route('aeds.index')->with('success', 'AED bijgewerkt!');
    }

    /**
     * Set AED to archief status (uit roulatie).
     */
    public function archive(Aed $aed): RedirectResponse
    {
        if ($aed->user_id !== auth()->id() || $aed->status !== 'actief') {
            abort(403);
        }

        $aed->update(['status' => 'archief']);

        return redirect()->route('aeds.index')->with('success', 'AED naar archief verplaatst met geschiedenis!');
    }

    /**
     * Remove the specified AED from database (only if archief).
     */
    public function destroy(Aed $aed): RedirectResponse
    {
        if ($aed->user_id !== auth()->id() || $aed->status !== 'archief') {
            return redirect()->route('aeds.index')->with('error', 'Kan alleen AEDs uit archief verwijderen.');
        }

        $aed->delete(); // soft delete

        return redirect()->route('aeds.index')->with('success', 'AED verwijderd!');
    }
}

