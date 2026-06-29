<?php

namespace App\Http\Controllers;

use App\Models\Aed;
use App\Models\AedBeheerafspraak;
use App\Models\ControleLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAedRequest;
use App\Http\Requests\UpdateAedRequest;

use Illuminate\Support\Facades\Storage;
use App\Services\AedExport\AedExportService;


class AedController extends Controller
{
    public function map()
    {
        return view('aeds.map');
    }

    public function mapLocations()
    {
        $aeds = Aed::query()
            ->where('status', 'actief')
            ->get([
                'id', 'adres', 'huisnummer', 'plaats', 'beschrijving',
                'aed_type', 'serienummer_aed', 'eigenaar',
            ]);

        return response()->json([
            'count' => $aeds->count(),
            'aeds' => $aeds,
        ]);
    }

    public function index()
    {
        $aeds = Aed::where('status', '!=', 'archief')->get();
        return view('aeds.index', compact('aeds'));
    }

    public function edit(Aed $aed)
    {
        $aed->load(['beheerafspraak', 'controleLogs.user']);
        return view('aeds.edit', ['aed' => $aed]);
    }

    public function update(UpdateAedRequest $request, Aed $aed)
    {
        $validated = $request->validated();

        $payload = $validated;
        unset($payload['pincode'], $payload['onderhoudscode']);

        if ($request->has('serienummer_aed')) {
            $payload['serienummer_aed'] = $request->input('serienummer_aed');
        }
        if ($request->has('serienummer_kast')) {
            $payload['serienummer_kast'] = $request->input('serienummer_kast');
        }

        $aed->update($payload);

        $encryptedUpdates = [];
        if (!empty($validated['pincode'] ?? null)) {
            $encryptedUpdates['pincode'] = $validated['pincode'];
        }
        if (!empty($validated['onderhoudscode'] ?? null)) {
            $encryptedUpdates['onderhoudscode'] = $validated['onderhoudscode'];
        }

        if (!empty($encryptedUpdates)) {
            try {
                foreach ($encryptedUpdates as $key => $value) {
                    $aed->{$key} = $value;
                }
                $aed->saveQuietly();
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                logger()->warning('aed.update: decrypt exception', ['aed_id' => $aed->id]);
            }
        }

        // Cooperation agreement
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update(['cooperation_agreement_path' => $path]);
        }

        // Photos
        if ($request->boolean('remove_photo')) {
            $disk = Storage::disk('public');
            foreach ($aed->photos as $photo) {
                $disk->delete($photo->path ?? '');
            }
            $aed->photos()->delete();
        }

        if ($request->hasFile('foto')) {
            $disk = Storage::disk('public');
            if (!$request->boolean('remove_photo')) {
                foreach ($aed->photos as $photo) {
                    $disk->delete($photo->path ?? '');
                }
                $aed->photos()->delete();
            }
            $path = $request->file('foto')->store('aed-photos', 'public');
            $aed->photos()->create(['path' => $path]);
        }

        // Beheerafspraak
        $beheer = $validated['beheerafspraak'] ?? null;
        if (is_array($beheer)) {
            $aed->beheerafspraak()->updateOrCreate([], [
                'is_beheerder'          => $beheer['is_beheerder'] ?? false,
                'voert_controles_uit'   => $beheer['voert_controles_uit'] ?? false,
                'beheert_in_hartslagnu' => $beheer['beheert_in_hartslagnu'] ?? false,
                'extern_onderhoud'      => $beheer['extern_onderhoud'] ?? false,
            ]);
        }

        return redirect()->route('aeds.show', $aed)
            ->with('success', 'AED succesvol bijgewerkt!');
    }

    public function archief()
    {
        $aeds = Aed::where('status', 'archief')->get();
        return view('aeds.archief', compact('aeds'));
    }

    public function create()
    {
        return view('aeds.create');
    }

    public function store(StoreAedRequest $request)
    {
        $validated = $request->validated();
        $aed = Aed::create($validated);

        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update(['cooperation_agreement_path' => $path]);
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('aed-photos', 'public');
            $aed->photos()->create(['path' => $path]);
        }

        if (!empty($validated['beheerafspraak'])) {
            AedBeheerafspraak::create([
                'aed_id'                => $aed->id,
                'is_beheerder'          => $validated['beheerafspraak']['is_beheerder'] ?? false,
                'voert_controles_uit'   => $validated['beheerafspraak']['voert_controles_uit'] ?? false,
                'beheert_in_hartslagnu' => $validated['beheerafspraak']['beheert_in_hartslagnu'] ?? false,
                'extern_onderhoud'      => $validated['beheerafspraak']['extern_onderhoud'] ?? false,
            ]);
        }

        return redirect()->route('aeds.index')
            ->with('success', 'AED succesvol aangemaakt!');
    }

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

    public function controle(Aed $aed)
    {
        return view('controles.controle', ['aed' => $aed]);
    }

    public function controleStore(Request $request, Aed $aed)
    {
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

        $updateBatterij = $request->boolean('update_batterij_vervaldatum');
        $updateElektroden = $request->boolean('update_elektroden_vervaldatum');

        if ($updateBatterij && empty($validated['batterij_vervaldatum'])) {
            return back()->withErrors(['batterij_vervaldatum' => 'Vul de nieuwe batterij vervaldatum in.'])->withInput();
        }
        if ($updateElektroden && empty($validated['elektroden_vervaldatum'])) {
            return back()->withErrors(['elektroden_vervaldatum' => 'Vul de nieuwe elektroden vervaldatum in.'])->withInput();
        }

        if ($updateBatterij) {
            $aed->update(['batterij_vervaldatum' => $validated['batterij_vervaldatum']]);
        }
        if ($updateElektroden) {
            $aed->update(['elektroden_vervaldatum' => $validated['elektroden_vervaldatum']]);
        }

        ControleLog::create([
            'aed_id' => $aed->id,
            'user_id' => $request->user()->id,
            'datum' => $validated['datum'],
            'bevindingen' => $validated['bevindingen'] ?? null,
            'storing' => $request->boolean('storing'),
            'bijzonderheden' => $validated['bijzonderheden'] ?? null,
        ]);

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

        return redirect()->route('aeds.show', $aed)
            ->with('success', 'Controle succesvol opgeslagen en gelogd!');
    }

    public function archive(Aed $aed)
    {
        $aed->update(['status' => 'archief']);
        return redirect()->route('aeds.index')->with('success', 'AED is gearchiveerd.');
    }

    public function unarchive(Aed $aed)
    {
        $aed->update(['status' => 'actief']);
        return redirect()->route('aeds.archief')->with('success', 'AED succesvol gede-archiveerd.');
    }

    public function destroy(Aed $aed)
    {
        $aed->delete();
        return redirect()->route('aeds.archief')->with('success', 'AED permanent verwijderd.');
    }

    public function controleHistory(Aed $aed)
    {
        $aed->load(['beheerafspraak', 'controleLogs.user']);
        $logs = $aed->controleLogs()->with('user')->orderByDesc('datum')->orderByDesc('id')->get();
        return view('controles.history', compact('aed', 'logs'));
    }

    public function viewCooperationAgreement(Aed $aed)
    {
        abort_unless($aed->cooperation_agreement_path, 404);
        $path = Storage::disk('public')->path($aed->cooperation_agreement_path);
        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    // ==================== EXPORT ====================

    public function exportAll(AedExportService $exportService)
    {
        $this->authorize('admin');
        return $exportService->exportAll();
    }

    public function exportOne(Aed $aed, AedExportService $exportService)
    {
        $this->authorize('admin');
        return $exportService->exportOne($aed);
    }
}

