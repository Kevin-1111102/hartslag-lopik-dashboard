<?php

namespace App\Http\Controllers;

use App\Models\Aed;
use App\Models\AedBeheerafspraak;
use Illuminate\Http\Request;

class AedController extends Controller
{
    /**
     * Display a listing of all active AEDs (excluding archived).
     */
    public function index()
    {
        $aeds = Aed::where('status', '!=', 'archief')->get();
        return view('aeds.index', compact('aeds'));
    }

    /**
     * Display a listing of archived AEDs.
     */
    public function archief()
    {
        $aeds = Aed::where('status', 'archief')->get();
        return view('aeds.archief', compact('aeds'));
    }

    /**
     * Show the form for creating a new AED.
     */
    public function create()
    {
        return view('aeds.create');
    }

    /**
     * Store a newly created AED in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eigenaar'               => 'required|string|max:255',
            'contactpersoon'         => 'nullable|string|max:255',
            'aed_type'               => 'required|string|max:255',
            'serienummer'            => 'nullable|string|max:255',
            'adres'                  => 'required|string|max:255',
            'huisnummer'             => 'required|string|max:50',
            'plaats'                 => 'required|string|max:255',
            'beschrijving'           => 'nullable|string',
            'security'               => 'nullable|string|max:255',
            'pincode'                => 'nullable|string|max:255',
            'onderhoudscode'         => 'nullable|string|max:255',
            'serienummer_kast'       => 'nullable|string|max:255',
            'serienummer_aed'        => 'nullable|string|max:255',
            'batterij_vervaldatum'   => 'nullable|date',
            'elektroden_vervaldatum' => 'nullable|date',
            'lokaal_contactpersoon'  => 'nullable|string|max:255',
            'opmerkingen'            => 'nullable|string',
            'status'                 => 'required|in:actief,inactief,vervangen,archief',
            'beheerafspraak'         => 'nullable|array',
            'beheerafspraak.is_beheerder'         => 'boolean',
            'beheerafspraak.voert_controles_uit'  => 'boolean',
            'beheerafspraak.beheert_in_hartslagnu' => 'boolean',
            'beheerafspraak.extern_onderhoud'     => 'boolean',
        ]);

        $aed = Aed::create($validated);

        // Create beheerafspraak if any checkbox is checked
        if (!empty($validated['beheerafspraak'])) {
            AedBeheerafspraak::create([
                'aed_id'                => $aed->id,
                'is_beheerder'          => $validated['beheerafspraak']['is_beheerder'] ?? false,
                'voert_controles_uit'   => $validated['beheerafspraak']['voert_controles_uit'] ?? false,
                'beheert_in_hartslagnu' => $validated['beheerafspraak']['beheert_in_hartslagnu'] ?? false,
                'extern_onderhoud'      => $validated['beheerafspraak']['extern_onderhoud'] ?? false,
            ]);
        }

        return redirect()->route('aeds.index')->with('success', 'AED succesvol aangemaakt!');
    }

    /**
     * Display the specified AED with beheerafspraak and latest controle log.
     */
    public function show(Aed $aed)
    {
        $aed->load(['beheerafspraak', 'controleLogs.user']);

        $latestControle = $aed->controleLogs()
            ->with('user')
            ->latest('datum')
            ->first();

        return view('aeds.show', compact('aed', 'latestControle'));
    }

    /**
     * Archive the specified AED (soft-remove from active list).
     */
    public function archive(Aed $aed)
    {
        $aed->update(['status' => 'archief']);

        return redirect()->route('aeds.index')->with('success', 'AED is gearchiveerd.');
    }

    /**
     * Unarchive the specified AED (reactivate).
     */
    public function unarchive(Aed $aed)
    {
        $aed->update(['status' => 'actief']);

        return redirect()->route('aeds.archief')->with('success', 'AED succesvol gede-archiveerd en reactief gemaakt.');
    }

    /**
     * Permanently delete the specified AED.
     */
    public function destroy(Aed $aed)
    {
        $aed->delete();

        return redirect()->route('aeds.archief')->with('success', 'AED permanent verwijderd.');
    }
}

