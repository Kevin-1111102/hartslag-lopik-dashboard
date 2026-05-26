<?php

namespace Database\Seeders;

use App\Models\Aed;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevInitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- USERS (one admin) ---
        User::updateOrCreate(
            ['email' => 'admin@test.nl'],
            [
                'name' => 'Admin Beheer',
                'password' => Hash::make('password'),
                'is_admin' => 1,
                'email_verified_at' => now(),
            ]
        );

        // Insert 6 AED records (as requested)
        $aedRows = [
            [
                'serienummer_aed' => 'D00000103225',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => null,
                'adres' => 'Benedeneind Noordzijde',
                'huisnummer' => '468',
                'plaats' => 'Benschop',
                'beschrijving' => 'Ingang buurthuis Triangel. Code 2018 en druk daarna op V. (AED Eigendom gemeente Lopik)',
                'security' => 'Pincode',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => null,
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2026-12-03',
                'elektroden_vervaldatum' => '2025-09-15',
            ],
            [
                'serienummer_aed' => 'D00000103299',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => null,
                'adres' => 'Kon. Wilhelminastraat',
                'huisnummer' => '27',
                'plaats' => 'Benschop',
                'beschrijving' => 'Sporthal Buitenkast Code 2015 afsluiten met een V. (AED Eigendom gemeente Lopik)',
                'security' => 'Pincode',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => null,
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2026-12-03',
                'elektroden_vervaldatum' => '2025-09-15',
            ],
            [
                'serienummer_aed' => 'D00000103341',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => null,
                'adres' => 'Kon. Maximastraat',
                'huisnummer' => '42',
                'plaats' => 'Lopik',
                'beschrijving' => 'Plus Supermarkt Oplaadpunt naast Rolluik, in buitenkast. (AED Eigendom gemeente Lopik)',
                'security' => 'Pincode',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => null,
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2026-12-03',
                'elektroden_vervaldatum' => '2025-08-28',
            ],
            [
                'serienummer_aed' => 'D00000281794',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => null,
                'adres' => 'Churchilllaan',
                'huisnummer' => '21',
                'plaats' => 'Lopik',
                'beschrijving' => 'Zwembad Lobeke Pantry badmeester (incl kindersleutel). (AED Eigendom gemeente Lopik)',
                'security' => 'Geen code',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => 'Nieuwe electroden 28-12-2027',
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2028-07-12',
                'elektroden_vervaldatum' => '2027-12-28',
            ],
            [
                'serienummer_aed' => 'D00000103310',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => '1831P706',
                'adres' => 'M.A.A. Schakelplein',
                'huisnummer' => '6',
                'plaats' => 'Lopik',
                'beschrijving' => 'Voorzijde pand (Zuidwestingang). (AED Eigendom gemeente Lopik)',
                'security' => 'Pincode',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => null,
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2026-12-03',
                'elektroden_vervaldatum' => '2025-09-15',
            ],
            [
                'serienummer_aed' => 'D00000103302',
                'eigenaar' => 'Gemeente Lopik',
                'contactpersoon' => 'Denise Alblas',
                'aed_type' => 'Cardiac Science - Powerheart G5',
                'serienummer' => null,
                'serienummer_kast' => null,
                'adres' => 'Nicolaas van Catsweg',
                'huisnummer' => '9',
                'plaats' => 'Lopik',
                'beschrijving' => 'VV Cabauw ballenkamer achter keuken Verzorgingsruimte. (AED Eigendom gemeente Lopik)',
                'security' => 'Geen code',
                'pincode' => 'CODE',
                'onderhoudscode' => 'CODE',
                'lokaal_contactpersoon' => null,
                'opmerkingen' => null,
                'cooperation_agreement_path' => null,
                'status' => 'actief',
                'batterij_vervaldatum' => '2026-12-03',
                'elektroden_vervaldatum' => '2025-09-15',
            ],
        ];

        foreach ($aedRows as $row) {
            Aed::updateOrCreate(
                ['serienummer_aed' => $row['serienummer_aed']],
                $row
            );
        }
    }
}

