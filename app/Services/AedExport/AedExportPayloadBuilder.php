<?php

namespace App\Services\AedExport;

use App\Models\Aed;
use App\Models\Notification;
use App\Support\DecryptSafe;

class AedExportPayloadBuilder
{
    public function buildPayloadAll(): array
    {
        $aeds = Aed::with(['beheerafspraak', 'controleLogs.user', 'photos'])
            ->orderByDesc('id')
            ->get();

        $aedIds = $aeds->pluck('id')->all();

        $notificationsByAedAndDate = collect();
        if (!empty($aedIds)) {
            $notificationsByAedAndDate = Notification::whereIn('aed_id', $aedIds)
                ->get()
                ->groupBy(fn ($n) => $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? ''));
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

    public function buildPayloadOne(Aed $aed): array
    {
        $aed->load(['beheerafspraak', 'controleLogs.user', 'photos']);

        $notifications = Notification::query()
            ->where('aed_id', $aed->id)
            ->get()
            ->groupBy(fn ($n) => $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? ''));

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

            'pincode' => DecryptSafe::decrypt($aed->getRawOriginal('pincode'), 'aed.pincode'),
            'onderhoudscode' => DecryptSafe::decrypt($aed->getRawOriginal('onderhoudscode'), 'aed.onderhoudscode'),

            'lokaal_contactpersoon' => $aed->lokaal_contactpersoon,
            'opmerkingen' => $aed->opmerkingen,
            'cooperation_agreement_path' => $aed->cooperation_agreement_path,

            'batterij_vervaldatum' => $aed->batterij_vervaldatum?->format('Y-m-d'),
            'elektroden_vervaldatum' => $aed->elektroden_vervaldatum?->format('Y-m-d'),

            'beheerafspraak' => $aed->beheerafspraak ? [
                'is_beheerder' => (bool) $aed->beheerafspraak->is_beheerder,
                'voert_controles_uit' => (bool) $aed->beheerafspraak->voert_controles_uit,
                'beheert_in_hartslagnu' => (bool) $aed->beheerafspraak->beheert_in_hartslagnu,
                'extern_onderhoud' => (bool) $aed->beheerafspraak->extern_onderhoud,
            ] : null,

            'photos' => $aed->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'path' => $photo->path,
                'caption' => $photo->caption,
            ])->values()->all(),

            'controleLogs' => $aed->controleLogs->map(fn ($log) => [
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
}

