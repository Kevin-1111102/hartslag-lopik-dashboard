<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aed extends Model
{
    /** @use HasFactory<\Database\Factories\AedFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'eigenaar',
        'contactpersoon',
        'aed_type',
        'serienummer',
        'adres',
        'huisnummer',
        'plaats',
        'beschrijving',
        'security',
        'pincode',
        'onderhoudscode',
        'serienummer_kast',
        'serienummer_aed',
        'batterij_vervaldatum',
        'elektroden_vervaldatum',
        'lokaal_contactpersoon',
        'opmerkingen',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pincode'                => 'encrypted',
            'onderhoudscode'         => 'encrypted',
            'batterij_vervaldatum'   => 'date',
            'elektroden_vervaldatum' => 'date',
        ];
    }

    /**
     * Get the beheerafspraak associated with this AED.
     */
    public function beheerafspraak(): HasOne
    {
        return $this->hasOne(AedBeheerafspraak::class);
    }

    /**
     * Get the controle logs for this AED.
     */
    public function controleLogs(): HasMany
    {
        return $this->hasMany(ControleLog::class);
    }
}
