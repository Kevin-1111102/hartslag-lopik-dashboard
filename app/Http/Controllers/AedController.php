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

class AedController extends Controller
{
    /**
     * Export all AEDs (incl. archived) to an .xlsx file.
     */
    public function export()
    {
        // Ensure admin-only (route already has middleware, but keep it explicit here)
        abort_unless(auth()->check() && auth()->user()->is_admin, 403, 'Toegang geweigerd. Alleen admins.');

        $aeds = Aed::query()
            ->with([
                'beheerafspraak',
                'controleLogs.user',
                'photos',
            ])
            ->get();

        // PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator(auth()->user()?->name ?? 'system');

        // Excel best practices: remove default sheet name issues
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('AED export');

        // Eén uniforme tabel-structuur.
        // Eén rij per (AED x controleLog) en één rij per (AED x foto).
        // Zo blijven kolommen altijd op exact dezelfde posities.
        $headers = [
            'RowType',
            'AedID',
            'AedStatus',
            'Eigenaar',
            'Contactpersoon',
            'AED Type',
            'Serienummer',
            'Adres',
            'Huisnummer',
            'Plaats',
            'Beschrijving',
            'Security',
            'Pincode',
            'Onderhoudscode',
            'Serienummer Kast',
            'Serienummer AED',
            'Batterij vervaldatum',
            'Elektroden vervaldatum',
            'Lokaal contactpersoon',
            'Opmerkingen',
            'Samenwerkingsafspraak path',

            // beheerafspraak
            'Is beheerder',
            'Voert controles uit',
            'Beheert in hartslag nu',
            'Extern onderhoud',

            // controle log
            'Controle datum',
            'Controle gebruiker',
            'Bevindingen',
            'Opslag/ Storing',
            'Bijzonderheden',

            // foto
            'Foto path',
            'Foto caption',
        ];


        $col = 1;
        foreach ($headers as $header) {
            $cell = $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $sheet->getStyleByColumnAndRow($col, 1)->getFont()->setBold(true);
            $col++;
        }

        $row = 2;

        foreach ($aeds as $aed) {
            $beheer = $aed->beheerafspraak;

            $base = [
                'aed' => $aed,
                'beheer' => $beheer,
            ];

            // AED base row
            $sheet->setCellValue("A{$row}", 'AED');
            $sheet->setCellValue("B{$row}", $aed->id);
            $sheet->setCellValue("C{$row}", (string) ($aed->status ?? ''));
            $sheet->setCellValue("D{$row}", (string) ($aed->eigenaar ?? ''));
            $sheet->setCellValue("E{$row}", (string) ($aed->contactpersoon ?? ''));
            $sheet->setCellValue("F{$row}", (string) ($aed->aed_type ?? ''));
            $sheet->setCellValue("G{$row}", (string) ($aed->serienummer ?? ''));
            $sheet->setCellValue("H{$row}", (string) ($aed->adres ?? ''));
            $sheet->setCellValue("I{$row}", (string) ($aed->huisnummer ?? ''));
            $sheet->setCellValue("J{$row}", (string) ($aed->plaats ?? ''));
            $sheet->setCellValue("K{$row}", (string) ($aed->beschrijving ?? ''));
            $sheet->setCellValue("L{$row}", (string) ($aed->security ?? ''));
            $sheet->setCellValue("M{$row}", (string) ($aed->pincode ?? ''));
            $sheet->setCellValue("N{$row}", (string) ($aed->onderhoudscode ?? ''));
            $sheet->setCellValue("O{$row}", (string) ($aed->serienummer_kast ?? ''));
            $sheet->setCellValue("P{$row}", (string) ($aed->serienummer_aed ?? ''));
            if (!empty($aed->batterij_vervaldatum)) {
                $sheet->setCellValue("Q{$row}", $aed->batterij_vervaldatum->format('Y-m-d'));
            } else {
                $sheet->setCellValue("Q{$row}", '');
            }
            if (!empty($aed->elektroden_vervaldatum)) {
                $sheet->setCellValue("R{$row}", $aed->elektroden_vervaldatum->format('Y-m-d'));
            } else {
                $sheet->setCellValue("R{$row}", '');
            }
            $sheet->setCellValue("S{$row}", (string) ($aed->lokaal_contactpersoon ?? ''));
            $sheet->setCellValue("T{$row}", (string) ($aed->opmerkingen ?? ''));
            $sheet->setCellValue("U{$row}", (string) ($aed->cooperation_agreement_path ?? ''));

            $sheet->setCellValue("V{$row}", (string) ($beheer?->is_beheerder ? 'ja' : 'nee'));
            $sheet->setCellValue("W{$row}", (string) ($beheer?->voert_controles_uit ? 'ja' : 'nee'));
            $sheet->setCellValue("X{$row}", (string) ($beheer?->beheert_in_hartslagnu ? 'ja' : 'nee'));
            $sheet->setCellValue("Y{$row}", (string) ($beheer?->extern_onderhoud ? 'ja' : 'nee'));

            $row++;

            // Controle logs
            foreach ($aed->controleLogs as $log) {
                // RowType
                $sheet->setCellValue("A{$row}", 'Controle');

                // AED basis (zelfde kolommen als header)
                $sheet->setCellValue("B{$row}", $aed->id);
                $sheet->setCellValue("C{$row}", (string) ($aed->status ?? ''));
                $sheet->setCellValue("D{$row}", (string) ($aed->eigenaar ?? ''));
                $sheet->setCellValue("E{$row}", (string) ($aed->contactpersoon ?? ''));
                $sheet->setCellValue("F{$row}", (string) ($aed->aed_type ?? ''));
                $sheet->setCellValue("G{$row}", (string) ($aed->serienummer ?? ''));
                $sheet->setCellValue("H{$row}", (string) ($aed->adres ?? ''));
                $sheet->setCellValue("I{$row}", (string) ($aed->huisnummer ?? ''));
                $sheet->setCellValue("J{$row}", (string) ($aed->plaats ?? ''));
                $sheet->setCellValue("K{$row}", (string) ($aed->beschrijving ?? ''));
                $sheet->setCellValue("L{$row}", (string) ($aed->security ?? ''));
                $sheet->setCellValue("M{$row}", (string) ($aed->pincode ?? ''));
                $sheet->setCellValue("N{$row}", (string) ($aed->onderhoudscode ?? ''));
                $sheet->setCellValue("O{$row}", (string) ($aed->serienummer_kast ?? ''));
                $sheet->setCellValue("P{$row}", (string) ($aed->serienummer_aed ?? ''));
                $sheet->setCellValue("Q{$row}", !empty($aed->batterij_vervaldatum) ? $aed->batterij_vervaldatum->format('Y-m-d') : '');
                $sheet->setCellValue("R{$row}", !empty($aed->elektroden_vervaldatum) ? $aed->elektroden_vervaldatum->format('Y-m-d') : '');
                $sheet->setCellValue("S{$row}", (string) ($aed->lokaal_contactpersoon ?? ''));
                $sheet->setCellValue("T{$row}", (string) ($aed->opmerkingen ?? ''));
                $sheet->setCellValue("U{$row}", (string) ($aed->cooperation_agreement_path ?? ''));

                // beheerafspraak
                $sheet->setCellValue("V{$row}", (string) ($beheer?->is_beheerder ? 'ja' : 'nee'));
                $sheet->setCellValue("W{$row}", (string) ($beheer?->voert_controles_uit ? 'ja' : 'nee'));
                $sheet->setCellValue("X{$row}", (string) ($beheer?->beheert_in_hartslagnu ? 'ja' : 'nee'));
                $sheet->setCellValue("Y{$row}", (string) ($beheer?->extern_onderhoud ? 'ja' : 'nee'));

                // controle log
                $sheet->setCellValue("Z{$row}", !empty($log->datum) ? $log->datum->format('Y-m-d') : '');
                $sheet->setCellValue("AA{$row}", (string) ($log->user?->name ?? ''));
                $sheet->setCellValue("AB{$row}", (string) ($log->bevindingen ?? ''));
                $sheet->setCellValue("AC{$row}", $log->storing === null ? '' : ($log->storing ? 'ja' : 'nee'));
                $sheet->setCellValue("AD{$row}", (string) ($log->bijzonderheden ?? ''));

                // foto columns leeg laten
                $sheet->setCellValue("AE{$row}", '');
                $sheet->setCellValue("AF{$row}", '');

                $row++;
            }


            // Fotos
            foreach ($aed->photos as $photo) {
                // RowType
                $sheet->setCellValue("A{$row}", 'Foto');

                // AED basis
                $sheet->setCellValue("B{$row}", $aed->id);
                $sheet->setCellValue("C{$row}", (string) ($aed->status ?? ''));
                $sheet->setCellValue("D{$row}", (string) ($aed->eigenaar ?? ''));
                $sheet->setCellValue("E{$row}", (string) ($aed->contactpersoon ?? ''));
                $sheet->setCellValue("F{$row}", (string) ($aed->aed_type ?? ''));
                $sheet->setCellValue("G{$row}", (string) ($aed->serienummer ?? ''));
                $sheet->setCellValue("H{$row}", (string) ($aed->adres ?? ''));
                $sheet->setCellValue("I{$row}", (string) ($aed->huisnummer ?? ''));
                $sheet->setCellValue("J{$row}", (string) ($aed->plaats ?? ''));
                $sheet->setCellValue("K{$row}", (string) ($aed->beschrijving ?? ''));
                $sheet->setCellValue("L{$row}", (string) ($aed->security ?? ''));
                $sheet->setCellValue("M{$row}", (string) ($aed->pincode ?? ''));
                $sheet->setCellValue("N{$row}", (string) ($aed->onderhoudscode ?? ''));
                $sheet->setCellValue("O{$row}", (string) ($aed->serienummer_kast ?? ''));
                $sheet->setCellValue("P{$row}", (string) ($aed->serienummer_aed ?? ''));
                $sheet->setCellValue("Q{$row}", !empty($aed->batterij_vervaldatum) ? $aed->batterij_vervaldatum->format('Y-m-d') : '');
                $sheet->setCellValue("R{$row}", !empty($aed->elektroden_vervaldatum) ? $aed->elektroden_vervaldatum->format('Y-m-d') : '');
                $sheet->setCellValue("S{$row}", (string) ($aed->lokaal_contactpersoon ?? ''));
                $sheet->setCellValue("T{$row}", (string) ($aed->opmerkingen ?? ''));
                $sheet->setCellValue("U{$row}", (string) ($aed->cooperation_agreement_path ?? ''));

                // beheerafspraak
                $sheet->setCellValue("V{$row}", (string) ($beheer?->is_beheerder ? 'ja' : 'nee'));
                $sheet->setCellValue("W{$row}", (string) ($beheer?->voert_controles_uit ? 'ja' : 'nee'));
                $sheet->setCellValue("X{$row}", (string) ($beheer?->beheert_in_hartslagnu ? 'ja' : 'nee'));
                $sheet->setCellValue("Y{$row}", (string) ($beheer?->extern_onderhoud ? 'ja' : 'nee'));

                // controle log columns leeg
                $sheet->setCellValue("Z{$row}", '');
                $sheet->setCellValue("AA{$row}", '');
                $sheet->setCellValue("AB{$row}", '');
                $sheet->setCellValue("AC{$row}", '');
                $sheet->setCellValue("AD{$row}", '');

                // foto columns
                $sheet->setCellValue("AE{$row}", (string) ($photo->path ?? ''));
                $sheet->setCellValue("AF{$row}", (string) ($photo->caption ?? ''));

                $row++;
            }

        }

        // Auto size (careful performance). Minimal: set a reasonable width for first columns.
        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Write XLSX to output stream and force immediate download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'aed-export.xlsx';

        // Ensure no BOM/warnings; set headers for correct streaming.
        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

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


        // Persist scalar columns
        $aed->update($validated);

        // Replace cooperation agreement upload (optional)
        if ($request->hasFile('cooperation_agreement')) {
            $path = $request->file('cooperation_agreement')->store('aed-cooperation-agreements', 'public');
            $aed->update([
                'cooperation_agreement_path' => $path,
            ]);
        }

        // Foto upload/replace/remove via AedPhoto (meerdere foto's mogelijk)
        // 1) Remove existing photo(s)
        if ($request->boolean('remove_photo')) {
            $disk = Storage::disk('public');

            foreach ($aed->photos as $existingPhoto) {
                if (!empty($existingPhoto->path)) {
                    $disk->delete($existingPhoto->path);
                }
            }

            $aed->photos()->delete();
        }

        // 2) Upload/replace new photo (if provided)
        if ($request->hasFile('foto')) {
            $disk = Storage::disk('public');

            // Upload nieuwe foto(s); als je remove_photo eerder koos, wordt er al gewist.
            // Als je alleen vervangt zonder remove_photo, vervangen we door te wissen en opnieuw te maken.
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
}

