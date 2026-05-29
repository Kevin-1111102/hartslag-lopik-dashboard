<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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

            'foto' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_photo' => 'nullable|boolean',

            'cooperation_agreement' => 'nullable|file|mimes:pdf,doc,docx,odt,rtf,txt|max:10240',
        ];
    }
}

