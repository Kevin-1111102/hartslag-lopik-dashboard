<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controle extends Model
{
    use HasFactory;

    protected $fillable = [
        'aed_id',
        'user_id',
        'controle_datum',
        'status_aed',
        'status_kast',
        'opmerkingen',
        'actie_nodig',
    ];

    /**
     * Relaties
     */
    public function aed()
    {
        return $this->belongsTo(Aed::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}