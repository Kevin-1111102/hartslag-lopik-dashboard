<?php

namespace App\Services\AedExport;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AedExcelExporter
{
    public function export(array $exportPayload)
    {
        $export = $exportPayload;
        $notificationsByAedAndDate = $exportPayload['_notificationsByAedAndDate'] ?? collect();

        $aedCount = $export['count'] ?? count($export['aeds'] ?? []);

        $filename = $aedCount > 1
            ? 'aed-export-' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            : 'aed-export-' . ($export['aeds'][0]['id'] ?? 'unknown') . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $headers = [
            'id', 'status', 'eigenaar', 'contactpersoon', 'aed_type', 'serienummer', 'serienummer_aed', 'serienummer_kast',
            'adres', 'huisnummer', 'plaats', 'beschrijving', 'security', 'pincode', 'onderhoudscode', 'lokaal_contactpersoon',
            'opmerkingen', 'cooperation_agreement_path', 'batterij_vervaldatum', 'elektroden_vervaldatum',
            'is_beheerder', 'voert_controles_uit', 'beheert_in_hartslagnu', 'extern_onderhoud',
            'photos_count', 'controleLogs_count'
        ];

        $spreadsheet = new Spreadsheet();
        $columnToLetter = fn (int $col) => Coordinate::stringFromColumnIndex($col);

        $setHeaderRow = function ($sheet, array $headers, int $row) use ($columnToLetter) {
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue($columnToLetter($col++) . $row, $header);
            }
        };

        // Sheet 1: AEDs
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

        // Sheet 2: ControleLogs
        $sheetLogs = $spreadsheet->createSheet();
        $sheetLogs->setTitle('ControleLogs');

        $logHeaders = [
            'aed_id', 'aed_status', 'controle_id', 'user_id', 'user_name', 'user_email', 'datum',
            'bevindingen', 'storing', 'batterij_vervangen', 'batterij_vervangen_datum',
            'elektroden_vervangen', 'elektroden_vervangen_datum', 'bijzonderheden'
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

