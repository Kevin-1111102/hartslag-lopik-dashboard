<?php

namespace App\Http\Controllers;

use App\Models\Aed;
use App\Models\Notification;
use App\Support\DecryptSafe;


class AedExportHelper
{
    /**
     * Build AED export payload ut for a single AED.
     */
    public static function buildPayloadForOneAed(Aed $aed): array
    {
        $aed->load(['beheerafspraak', 'controleLogs.user', 'photos']);

        $notificationsByAedAndDate = collect();
        $aedId = $aed->id;

        $notifications = Notification::query()
            ->where('aed_id', $aedId)
            ->get()
            ->groupBy(function ($n) {
                return $n->aed_id . '|' . ($n->datum?->format('Y-m-d') ?? '');
            });

        $notificationsByAedAndDate = $notifications;

        $export = [
            'exported_at' => now()->toIso8601String(),
            'count' => 1,
            'aeds' => [],
        ];

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
            'pincode' => DecryptSafe::decrypt($aed->getRawOriginal('pincode'), 'aed.pincode'),
            'onderhoudscode' => DecryptSafe::decrypt($aed->getRawOriginal('onderhoudscode'), 'aed.onderhoudscode'),
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
            // helper data
            '_notificationsByAedAndDate' => $notificationsByAedAndDate,
        ];

        return $export;
    }
}

