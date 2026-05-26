<?php

namespace App\Http\Controllers;

use App\Models\Aed;
use App\Models\AedBeheerafspraak;
use App\Models\ControleLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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
     * Show the form for editing the specified AED.
     */
    public function edit(Aed $aed)
    {
        $aed->load(['beheerafspraak', 'controleLogs.user']);

        return view('aeds.edit', [
            'aed' => $aed,
        ]);
    }

    /**
     * Update the specified AED in storage.
     */
    public function update(Request $request, Aed $aed)
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
            'cooperation_agreement'             => 'nullable|file|mimes:pdf,doc,docx,odt,rtf,txt|max:10240',
        ]);

        $aed->update($validated);

        // Replace optional cooperation agreement upload
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update([
                'cooperation_agreement_path' => $path,
            ]);
        }

        // Upsert beheerafspraak based on checkbox values
        $beheer = $validated['beheerafspraak'] ?? null;
        if (is_array($beheer)) {
            $isBeheerder = $beheer['is_beheerder'] ?? false;
            $voertControlesUit = $beheer['voert_controles_uit'] ?? false;
            $beheertInHartslagNu = $beheer['beheert_in_hartslagnu'] ?? false;
            $externOnderhoud = $beheer['extern_onderhoud'] ?? false;

            $aed->beheerafspraak()->updateOrCreate([], [
                'is_beheerder' => $isBeheerder,
                'voert_controles_uit' => $voertControlesUit,
                'beheert_in_hartslagnu' => $beheertInHartslagNu,
                'extern_onderhoud' => $externOnderhoud,
            ]);
        }

        return redirect()->route('aeds.show', $aed)->with('success', 'AED succesvol bijgewerkt!');
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
            'cooperation_agreement'             => 'nullable|file|mimes:pdf,doc,docx,odt,rtf,txt|max:10240',
        ]);

        $aed = Aed::create($validated);

        // Store optional cooperation agreement upload
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update([
                'cooperation_agreement_path' => $path,
            ]);
        }

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
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->first();

        return view('aeds.show', compact('aed', 'latestControle'));
    }

    /**
     * Show controle-scherm for an AED (battery/electrodes update + full log).
     */
    public function controle(Aed $aed)
    {
        return view('controles.controle', [
            'aed' => $aed,
        ]);
    }

    /**
     * Store controle data and create ControleLog.
     */
    public function controleStore(Request $request, Aed $aed)
    {

        logger()->warning('controleStore called', [
            'aed_id' => $aed->id,
            'user_id' => $request->user()?->id,
            'request_id' => (string) $request->headers->get('X-Request-Id'),
            'storing_present' => $request->has('storing'),
            'storing_value' => $request->input('storing'),
            'payload_datum' => $request->input('datum'),
        ]);

        $validated = $request->validate([
            'datum' => 'required|date',
            'storing' => 'nullable|boolean',
            'bevindingen' => 'nullable|string',
            'bijzonderheden' => 'nullable|string',

            'update_batterij_vervaldatum' => 'nullable|boolean',
            'batterij_vervaldatum' => 'nullable|date',

            'update_elektroden_vervaldatum' => 'nullable|boolean',
            'elektroden_vervaldatum' => 'nullable|date',
        ]);

        // Normalize checkboxes: if checkbox not present, validate returns null.
        $updateBatterij = $request->boolean('update_batterij_vervaldatum');
        $updateElektroden = $request->boolean('update_elektroden_vervaldatum');

        if ($updateBatterij && empty($validated['batterij_vervaldatum'])) {
            return back()->withErrors(['batterij_vervaldatum' => 'Vul de nieuwe batterij vervaldatum in.'])->withInput();
        }

        if ($updateElektroden && empty($validated['elektroden_vervaldatum'])) {
            return back()->withErrors(['elektroden_vervaldatum' => 'Vul de nieuwe elektroden vervaldatum in.'])->withInput();
        }

        // Update AED dates conditionally
        if ($updateBatterij) {
            $aed->update(['batterij_vervaldatum' => $validated['batterij_vervaldatum']]);
        }

        if ($updateElektroden) {
            $aed->update(['elektroden_vervaldatum' => $validated['elektroden_vervaldatum']]);
        }

        // Create ControleLog
        ControleLog::create([
            'aed_id' => $aed->id,
            'user_id' => $request->user()->id,
            'datum' => $validated['datum'],
            'bevindingen' => $validated['bevindingen'] ?? null,
            'storing' => $request->boolean('storing'),
            'bijzonderheden' => $validated['bijzonderheden'] ?? null,
        ]);

        // Belangrijk: storing mag niet blijven hangen op een oude controle.
        // Als de storing-checkbox niet is aangezet, sla dan storing=false op in de nieuwe log.
        // (request->boolean('storing') levert false als de checkbox ontbreekt)

        // Create Notifications (simple admin alerts)
        if ($updateBatterij) {
            Notification::create([
                'type' => 'batterij',
                'aed_id' => $aed->id,
                'bericht' => 'Batterij is vervangen bij AED #' . $aed->id,
                'datum' => $validated['datum'],
                'gelezen' => false,
            ]);
        }

        if ($updateElektroden) {
            Notification::create([
                'type' => 'elektroden',
                'aed_id' => $aed->id,
                'bericht' => 'Elektroden zijn vervangen bij AED #' . $aed->id,
                'datum' => $validated['datum'],
                'gelezen' => false,
            ]);
        }

        return redirect()->route('aeds.show', $aed)->with('success', 'Controle succesvol opgeslagen en gelogd!');
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

    public function controleHistory(Aed $aed)
    {
        $aed->load(['beheerafspraak', 'controleLogs.user']);

        $logs = $aed->controleLogs()
            ->with('user')
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->get();

        return view('controles.history', compact('aed', 'logs'));
    }

    public function viewCooperationAgreement(Aed $aed)
    {
        abort_unless($aed->cooperation_agreement_path, 404);


        $path = $aed->cooperation_agreement_path;
        $absolutePath = Storage::disk('public')->path($path);

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="'.basename($absolutePath).'"',
        ]);
    }
}


