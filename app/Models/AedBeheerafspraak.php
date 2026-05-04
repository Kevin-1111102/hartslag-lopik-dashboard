<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AedBeheerafspraak extends Model
{
    /** @use HasFactory<\Database\Factories\AedBeheerafspraakFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aed_beheerafspraken';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [

        'aed_id',
        'is_beheerder',
        'voert_controles_uit',
        'beheert_in_hartslagnu',
        'extern_onderhoud',
    ];

    /**
     * Get the AED that owns this beheerafspraak.
     */
    public function aed(): BelongsTo
    {
        return $this->belongsTo(Aed::class);
    }
}
