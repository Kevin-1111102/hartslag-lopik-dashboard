<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControleLog extends Model
{
    /** @use HasFactory<\Database\Factories\ControleLogFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'aed_id',
        'user_id',
        'datum',
        'bevindingen',
        'storing',
        'bijzonderheden',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datum' => 'date',
        ];
    }

    /**
     * Get the AED that owns this controle log.
     */
    public function aed(): BelongsTo
    {
        return $this->belongsTo(Aed::class);
    }

    /**
     * Get the user that performed this controle.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
