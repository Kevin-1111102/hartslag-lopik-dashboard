<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aed extends Model
{
    use HasFactory;
    use SoftDeletes;

    
    protected $fillable = [
        'type',
        'serienummer',
        'adres',
        'huisnummer',
        'plaats',
        'beschrijving',
        'pincode',
        'onderhoudscode',
        'batterij_vervaldatum',
        'elektroden_vervaldatum',
        'user_id',
        'shl_beheerder',
        'shl_verantwoordelijk_controle',
        'shl_hartslagnu_beheer',
        'externe_onderhoud',
        'status',
    ];

    protected $casts = [
        'batterij_vervaldatum' => 'date',
        'elektroden_vervaldatum' => 'date',
        'shl_beheerder' => 'boolean',
        'shl_verantwoordelijk_controle' => 'boolean',
        'shl_hartslagnu_beheer' => 'boolean',
        'externe_onderhoud' => 'boolean',
        'status' => 'string',
    ];

    /**
     * Relatie: Een AED behoort tot een Gebruiker (Beheerder).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relatie: Een AED kan meerdere controles hebben.
     */
    public function controles()
    {
        return $this->hasMany(Controle::class);
    }

    /**
     * Scope voor AED's die aandacht nodig hebben.
     * Filtert op batterijen of elektroden die binnen 30 dagen verlopen.
     */
    public function scopeNeedsAttention($query)
    {
        return $query->where('batterij_vervaldatum', '<', now()->addDays(30))
                     ->orWhere('elektroden_vervaldatum', '<', now()->addDays(30));
    }

    public function scopeActief($query)
    {
        return $query->where('status', 'actief');
    }

    public function scopeArchief($query)
    {
        return $query->where('status', 'archief');
    }

    public function scopeVerwijderd($query)
    {
        return $query->where('status', 'verwijderd');
    }
}
