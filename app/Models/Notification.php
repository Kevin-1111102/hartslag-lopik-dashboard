<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'aed_id',
        'bericht',
        'datum',
        'gelezen',
    ];

    protected function casts(): array
    {
        return [
            'datum' => 'date',
            'gelezen' => 'boolean',
        ];
    }

    public function aed(): BelongsTo
    {
        return $this->belongsTo(Aed::class);
    }
}

