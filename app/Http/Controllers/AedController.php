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

    public function exportAll()
    {
        $this->authorize('admin');
        return $this->exportSpreadsheet($this->buildExportPayloadAll());
    }

    public function exportOne(Aed $aed)
    {
        $this->authorize('admin');
        return $this->exportSpreadsheet($this->buildExportPayloadOne($aed));
    }

    private function buildExportPayloadAll(): array
    {
        $aeds = Aed::with(['beheerafspraak', 'controleLogs.user', 'photos'])
            ->orderByDesc('id')
            ->get();

        $aedIds = $aeds->pluck('id')->all();

        $notificationsByAedAndDate = collect();
        if (!empty($aedIds)) {
            $notifications = Notification::whereIn('aed_id', $aedIds)
                ->get()
                ->groupBy(fn($n) => $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? ''));

            $notificationsByAedAndDate = $notifications;
        }

        $export = [
            'exported_at' => now()->toIso8601String(),
            'count' => $aeds->count(),
            'aeds' => [],
            '_notificationsByAedAndDate' => $notificationsByAedAndDate,
        ];

        foreach ($aeds as $aed) {
            $export['aeds'][] = $this->buildAedExportArray($aed);
        }

        return $export;
    }

    private function buildExportPayloadOne(Aed $aed): array
    {
        $aed->load(['beheerafspraak', 'controleLogs.user', 'photos']);

        $notifications = Notification::where('aed_id', $aed->id)
            ->get()
            ->groupBy(fn($n) => $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? ''));

        $export = [
            'exported_at' => now()->toIso8601String(),
            'count' => 1,
            'aeds' => [],
            '_notificationsByAedAndDate' => $notifications,
        ];

        $export['aeds'][] = $this->buildAedExportArray($aed);

        return $export;
    }

    private function buildAedExportArray(Aed $aed): array
    {
        return [
            'id' => $aed->id,
            'status' => $aed->status,
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

            'pincode' => \App\Support\DecryptSafe::decrypt($aed->getRawOriginal('pincode'), 'aed.pincode'),
            'onderhoudscode' => \App\Support\DecryptSafe::decrypt($aed->getRawOriginal('onderhoudscode'), 'aed.onderhoudscode'),

            'lokaal_contactpersoon' => $aed->lokaal_contactpersoon,
            'opmerkingen' => $aed->opmerkingen,
            'cooperation_agreement_path' => $aed->cooperation_agreement_path,

            'batterij_vervaldatum' => $aed->batterij_vervaldatum?->format('Y-m-d'),
            'elektroden_vervaldatum' => $aed->elektroden_vervaldatum?->format('Y-m-d'),

            'beheerafspraak' => $aed->beheerafspraak ? [
                'is_beheerder'          => (bool) $aed->beheerafspraak->is_beheerder,
                'voert_controles_uit'   => (bool) $aed->beheerafspraak->voert_controles_uit,
                'beheert_in_hartslagnu' => (bool) $aed->beheerafspraak->beheert_in_hartslagnu,
                'extern_onderhoud'      => (bool) $aed->beheerafspraak->extern_onderhoud,
            ] : null,

            'photos' => $aed->photos->map(fn($photo) => [
                'id' => $photo->id,
                'path' => $photo->path,
                'caption' => $photo->caption,
            ])->values()->all(),

            'controleLogs' => $aed->controleLogs->map(fn($log) => [
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
            ])->values()->all(),
        ];
    }

    private function exportSpreadsheet(array $exportPayload)
    {
        $export = $exportPayload;
        $notificationsByAedAndDate = $exportPayload['_notificationsByAedAndDate'] ?? collect();

        $aedCount = $export['count'] ?? count($export['aeds'] ?? []);

        $filename = $aedCount > 1
            ? 'aed-export-' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            : 'aed-export-' . ($export['aeds'][0]['id'] ?? 'unknown') . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $headers = [
            'id','status','eigenaar','contactpersoon','aed_type','serienummer','serienummer_aed','serienummer_kast',
            'adres','huisnummer','plaats','beschrijving','security','pincode','onderhoudscode','lokaal_contactpersoon',
            'opmerkingen','cooperation_agreement_path','batterij_vervaldatum','elektroden_vervaldatum',
            'is_beheerder','voert_controles_uit','beheert_in_hartslagnu','extern_onderhoud',
            'photos_count','controleLogs_count'
        ];

        $spreadsheet = new Spreadsheet();
        $columnToLetter = fn(int $col) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

        $setHeaderRow = function ($sheet, array $headers, int $row) use ($columnToLetter) {
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue($columnToLetter($col++) . $row, $header);
            }
        };

        // === Sheet 1: AEDs ===
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
                $aed['beheerafspraak']['is_beheerder'] ?? null,
                $aed['beheerafspraak']['voert_controles_uit'] ?? null,
                $aed['beheerafspraak']['beheert_in_hartslagnu'] ?? null,
                $aed['beheerafspraak']['extern_onderhoud'] ?? null,
                count($aed['photos'] ?? []),
                count($aed['controleLogs'] ?? []),
            ];

            $col = 1;
            foreach ($values as $value) {
                $sheetAeds->setCellValue($columnToLetter($col++) . $rowIndex, $value);
            }
            $rowIndex++;
        }

        // === Sheet 2: ControleLogs ===
        $sheetLogs = $spreadsheet->createSheet();
        $sheetLogs->setTitle('ControleLogs');

        $logHeaders = [
            'aed_id','aed_status','controle_id','user_id','user_name','user_email','datum',
            'bevindingen','storing','batterij_vervangen','batterij_vervangen_datum',
            'elektroden_vervangen','elektroden_vervangen_datum','bijzonderheden'
        ];
        $setHeaderRow($sheetLogs, $logHeaders, 1);

        $logRowIndex = 2;
        foreach ($export['aeds'] as $aed) {
            foreach (($aed['controleLogs'] ?? []) as $log) {
                $dateKey = $aed['id'] . '|' . ($log['datum'] ?? '');
                $notifForDate = $notificationsByAedAndDate->get($dateKey) ?? collect();

                $batterijNotif = $notifForDate->firstWhere('type', 'batterij');
                $elektrodenNotif = $notifForDate->firstWhere('type', 'elektroden');

                $values = [
                    $aed['id'],
                    $aed['status'],
                    $log['id'] ?? null,
                    $log['user']['id'] ?? null,
                    $log['user']['name'] ?? null,
                    $log['user']['email'] ?? null,
                    $log['datum'] ?? null,
                    $log['bevindingen'] ?? null,
                    $log['storing'] ?? null,
                    $batterijNotif ? 1 : 0,
                    $batterijNotif?->datum?->format('Y-m-d'),
                    $elektrodenNotif ? 1 : 0,
                    $elektrodenNotif?->datum?->format('Y-m-d'),
                    $log['bijzonderheden'] ?? null,
                ];

                $col = 1;
                foreach ($values as $value) {
                    $sheetLogs->setCellValue($columnToLetter($col++) . $logRowIndex, $value);
                }
                $logRowIndex++;
            }
        }

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