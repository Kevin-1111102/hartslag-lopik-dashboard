<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AedPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'aed_id',
        'path',
        'caption',
    ];

    public function aed(): BelongsTo
    {
        return $this->belongsTo(Aed::class);
    }
}

