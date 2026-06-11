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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    public function update(UpdateAedRequest $request, Aed $aed)
    {
        $validated = $request->validated();

        $payload = $validated;
        unset($payload['pincode'], $payload['onderhoudscode']);

        // Keep serial numbers if they were sent from the form (they are displayed on the AED page).
        if ($request->has('serienummer_aed')) {
            $payload['serienummer_aed'] = $request->input('serienummer_aed');
        }
        if ($request->has('serienummer_kast')) {
            $payload['serienummer_kast'] = $request->input('serienummer_kast');
        }

        $aed->update($payload);

        $encryptedUpdates = [];

        if (array_key_exists('pincode', $validated) && $validated['pincode'] !== null && $validated['pincode'] !== '') {
            $encryptedUpdates['pincode'] = $validated['pincode'];
        }
        if (array_key_exists('onderhoudscode', $validated) && $validated['onderhoudscode'] !== null && $validated['onderhoudscode'] !== '') {
            $encryptedUpdates['onderhoudscode'] = $validated['onderhoudscode'];
        }

        if (!empty($encryptedUpdates)) {
            try {
                foreach ($encryptedUpdates as $key => $value) {
                    // Assign directly to avoid decrypting old encrypted values via fill().
                    $aed->{$key} = $value;
                }

                // If decrypting fails (legacy bad ciphertext), keep the update working.
                $aed->saveQuietly();
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                logger()->warning('aed.update: decrypt exception while saving encrypted fields', [
                    'aed_id' => $aed->id,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);
                // Intentionally swallow so the overall update succeeds.
            }
        }

        // Upload cooperation agreement (optional).
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update([
                'cooperation_agreement_path' => $path,
            ]);
        }

        if ($request->boolean('remove_photo')) {
            $disk = Storage::disk('public');

            foreach ($aed->photos as $existingPhoto) {
                if (!empty($existingPhoto->path)) {
                    $disk->delete($existingPhoto->path);
                }
            }

            $aed->photos()->delete();
        }

        // If a new photo is uploaded, replace existing ones (unless remove_photo already ran).
        if ($request->hasFile('foto')) {
            $disk = Storage::disk('public');

            // If we didn't already remove photos, replace by deleting existing ones first.
            if (!$request->boolean('remove_photo')) {
                foreach ($aed->photos as $existingPhoto) {
                    if (!empty($existingPhoto->path)) {
                        $disk->delete($existingPhoto->path);
                    }
                }
                $aed->photos()->delete();
            }

            $path = $request->file('foto')->store('aed-photos', 'public');

            $aed->photos()->create([
                'path' => $path,
            ]);
        }

        // Update (or create) beheerafspraak based on the checkbox values from the form.
        $beheer = $validated['beheerafspraak'] ?? null;
        if (is_array($beheer)) {
            $aed->beheerafspraak()->updateOrCreate([], [
                'is_beheerder' => $beheer['is_beheerder'] ?? false,
                'voert_controles_uit' => $beheer['voert_controles_uit'] ?? false,
                'beheert_in_hartslagnu' => $beheer['beheert_in_hartslagnu'] ?? false,
                'extern_onderhoud' => $beheer['extern_onderhoud'] ?? false,
            ]);
        }

        return redirect()->route('aeds.show', $aed)->with('success', 'AED succesvol bijgewerkt!');
    }

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
    public function store(StoreAedRequest $request)
    {
        $validated = $request->validated();


        $aed = Aed::create($validated);

        // Store optional cooperation agreement upload
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update([
                'cooperation_agreement_path' => $path,
            ]);
        }

        // Store optional photo upload
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('aed-photos', 'public');
            $aed->photos()->create([
                'path' => $path,
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
        if (config('app.debug')) {
            logger()->debug('controleStore called', [
                'aed_id' => $aed->id,
                'user_id' => $request->user()?->id,
                'request_id' => (string) $request->headers->get('X-Request-Id'),
                'storing_present' => $request->has('storing'),
                'storing_value' => $request->input('storing'),
                'payload_datum' => $request->input('datum'),
            ]);
        }


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
            'Content-Disposition' => 'inline; filename="' . basename($absolutePath) . '"',
        ]);
    }

    /**
     * Download JSON export of all AED data (including archived).
     * Admin-only (because it includes decrypted pincode/onderhoudscode).
     */
    public function exportAll()
    {
        $this->authorize('admin');

        $aeds = Aed::with(['beheerafspraak', 'controleLogs.user', 'photos'])
            ->orderByDesc('id')
            ->get();

        // Load notifications so we can export "iets vervangen" per controle moment.
        // ControleStore creates notifications with types:
        // - batterij
        // - elektroden
        $aedIds = $aeds->pluck('id')->values()->all();
        $notificationsByAedAndDate = collect();
        if (!empty($aedIds)) {
            $notifications = Notification::whereIn('aed_id', $aedIds)
                ->get()
                ->groupBy(function ($n) {
                    return $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? '');
                });

            $notificationsByAedAndDate = $notifications;
        }

        $export = [
            'exported_at' => now()->toIso8601String(),
            'count' => $aeds->count(),
            'aeds' => [],
        ];

        foreach ($aeds as $aed) {
            $export['aeds'][] = [
                'id' => $aed->id,
                'eigenaar' => $aed->eigenaar,
                'contactpersoon' => $aed->contactpersoon,
                'aed_type' => $aed->aed_type,
                'serienummer' => $aed->serienummer,
                'serienummer_aed' => $aed->serienummer_aed,
                'serienummer_kast' => $aed->serienummer_kast,
                'adres' => $aed->adres,
                'huisnummer' => $aed->huisnummer,
                'plaats' => $aed->plaats,
                'beschrijving' => $aed->beschrijving,
                'security' => $aed->security,

                // Decrypt directly to avoid touching encrypted casts via the model.
                'pincode' => \App\Support\DecryptSafe::decrypt($aed->getRawOriginal('pincode'), 'aed.pincode'),
                'onderhoudscode' => \App\Support\DecryptSafe::decrypt($aed->getRawOriginal('onderhoudscode'), 'aed.onderhoudscode'),

                'lokaal_contactpersoon' => $aed->lokaal_contactpersoon,
                'opmerkingen' => $aed->opmerkingen,
                'cooperation_agreement_path' => $aed->cooperation_agreement_path,
                'status' => $aed->status,

                'batterij_vervaldatum' => $aed->batterij_vervaldatum?->format('Y-m-d'),
                'elektroden_vervaldatum' => $aed->elektroden_vervaldatum?->format('Y-m-d'),

                'beheerafspraak' => $aed->beheerafspraak ? [
                    'is_beheerder' => (bool) $aed->beheerafspraak->is_beheerder,
                    'voert_controles_uit' => (bool) $aed->beheerafspraak->voert_controles_uit,
                    'beheert_in_hartslagnu' => (bool) $aed->beheerafspraak->beheert_in_hartslagnu,
                    'extern_onderhoud' => (bool) $aed->beheerafspraak->extern_onderhoud,
                ] : null,

                'photos' => $aed->photos->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'path' => $photo->path,
                        'caption' => $photo->caption,
                    ];
                })->values()->all(),

                'controleLogs' => $aed->controleLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'user' => [
                            'id' => $log->user?->id,
                            'name' => $log->user?->name,
                            'email' => $log->user?->email,
                        ],
                        'datum' => $log->datum?->format('Y-m-d'),
                        'bevindingen' => $log->bevindingen,
                        'storing' => (bool) $log->storing,
                        'bijzonderheden' => $log->bijzonderheden,
                    ];
                })->values()->all(),
            ];
        }

        // Export als echte Excel (.xlsx)
        $filename = 'aed-export-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Kolommen voor AEDs (flat).
        $headers = [
            'id',
            'status',
            'eigenaar',
            'contactpersoon',
            'aed_type',
            'serienummer',
            'serienummer_aed',
            'serienummer_kast',
            'adres',
            'huisnummer',
            'plaats',
            'beschrijving',
            'security',
            'pincode',
            'onderhoudscode',
            'lokaal_contactpersoon',
            'opmerkingen',
            'cooperation_agreement_path',
            'batterij_vervaldatum',
            'elektroden_vervaldatum',
            'is_beheerder',
            'voert_controles_uit',
            'beheert_in_hartslagnu',
            'extern_onderhoud',
            'photos_count',
            'controleLogs_count',
        ];

        $spreadsheet = new Spreadsheet();

        $columnToLetter = function (int $columnIndex): string {
            return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        };

        // Helper: write header row (Excel column letters are needed because we don't have setCellValueByColumnAndRow in this PhpSpreadsheet version).
        $setHeaderRow = function ($worksheet, array $headers, int $rowIndex) use ($columnToLetter) {
            $colIndex = 1;
            foreach ($headers as $header) {
                $worksheet->setCellValue($columnToLetter($colIndex) . $rowIndex, $header);
                $colIndex++;
            }
        };

        // Sheet 1: AEDs (one row per AED)
        $sheetAeds = $spreadsheet->getActiveSheet();
        $sheetAeds->setTitle('AEDs');

        $setHeaderRow($sheetAeds, $headers, 1);

        $rowIndex = 2;
        foreach ($export['aeds'] as $aed) {
            $values = [
                $aed['id'],
                $aed['status'],
                $aed['eigenaar'],
                $aed['contactpersoon'],
                $aed['aed_type'],
                $aed['serienummer'],
                $aed['serienummer_aed'],
                $aed['serienummer_kast'],
                $aed['adres'],
                $aed['huisnummer'],
                $aed['plaats'],
                $aed['beschrijving'],
                $aed['security'],
                $aed['pincode'],
                $aed['onderhoudscode'],
                $aed['lokaal_contactpersoon'],
                $aed['opmerkingen'],
                $aed['cooperation_agreement_path'],
                $aed['batterij_vervaldatum'],
                $aed['elektroden_vervaldatum'],
                (isset($aed['beheerafspraak']['is_beheerder']) ? ((int) $aed['beheerafspraak']['is_beheerder']) : null),
                (isset($aed['beheerafspraak']['voert_controles_uit']) ? ((int) $aed['beheerafspraak']['voert_controles_uit']) : null),
                (isset($aed['beheerafspraak']['beheert_in_hartslagnu']) ? ((int) $aed['beheerafspraak']['beheert_in_hartslagnu']) : null),
                (isset($aed['beheerafspraak']['extern_onderhoud']) ? ((int) $aed['beheerafspraak']['extern_onderhoud']) : null),
                is_array($aed['photos']) ? count($aed['photos']) : 0,
                is_array($aed['controleLogs']) ? count($aed['controleLogs']) : 0,
            ];

            $colIndex = 1;
            foreach ($values as $value) {
                $sheetAeds->setCellValue($columnToLetter($colIndex) . $rowIndex, $value);
                $colIndex++;
            }

            $rowIndex++;
        }

        // Sheet 2: ControleLogs (one row per controleLog)
        $sheetLogs = $spreadsheet->createSheet();
        $sheetLogs->setTitle('ControleLogs');

        $logHeaders = [
            'aed_id',
            'aed_status',
            'controle_id',
            'user_id',
            'user_name',
            'user_email',
            'datum',
            'bevindingen',
            'storing',
            'batterij_vervangen',
            'batterij_vervangen_datum',
            'elektroden_vervangen',
            'elektroden_vervangen_datum',
            'bijzonderheden',
        ];

        $setHeaderRow($sheetLogs, $logHeaders, 1);

        $logRowIndex = 2;
        foreach ($export['aeds'] as $aed) {
            foreach (($aed['controleLogs'] ?? []) as $log) {
                $controleDate = $log['datum'] ?? null;
                $controleDateStr = null;
                if ($controleDate) {
                    $controleDateStr = $controleDate instanceof \Illuminate\Support\Carbon
                        ? $controleDate->format('Y-m-d')
                        : (string) $controleDate;
                }

                $dateKey = $aed['id'] . '|' . ($controleDateStr ?? '');
                $notifForDate = $notificationsByAedAndDate->get($dateKey) ?? collect();

                $batterijNotif = $notifForDate->firstWhere('type', 'batterij');
                $elektrodenNotif = $notifForDate->firstWhere('type', 'elektroden');

                $batterijVervangen = $batterijNotif ? 1 : 0;
                $elektrodenVervangen = $elektrodenNotif ? 1 : 0;

                $batterijVervangenDatum = $batterijNotif?->datum instanceof \Illuminate\Support\Carbon
                    ? $batterijNotif->datum->format('Y-m-d')
                    : ($batterijNotif?->datum ? (string) $batterijNotif->datum : null);

                $elektrodenVervangenDatum = $elektrodenNotif?->datum instanceof \Illuminate\Support\Carbon
                    ? $elektrodenNotif->datum->format('Y-m-d')
                    : ($elektrodenNotif?->datum ? (string) $elektrodenNotif->datum : null);

                $values = [
                    $aed['id'],
                    $aed['status'],
                    $log['id'] ?? null,
                    $log['user']['id'] ?? null,
                    $log['user']['name'] ?? null,
                    $log['user']['email'] ?? null,
                    $log['datum'] ?? null,
                    $log['bevindingen'] ?? null,
                    (isset($log['storing']) ? ((int) $log['storing']) : null),
                    $batterijVervangen,
                    $batterijVervangenDatum,
                    $elektrodenVervangen,
                    $elektrodenVervangenDatum,
                    $log['bijzonderheden'] ?? null,
                ];

                $colIndex = 1;
                foreach ($values as $value) {
                    $sheetLogs->setCellValue($columnToLetter($colIndex) . $logRowIndex, $value);
                    $colIndex++;
                }

                $logRowIndex++;
            }
        }

        // Schrijf naar geheugen i.p.v. bestand
        $writer = new Xlsx($spreadsheet);
        $temp = fopen('php://temp', 'r+');
        $writer->save($temp);
        rewind($temp);
        $xlsx = stream_get_contents($temp);
        fclose($temp);

        return response($xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}


