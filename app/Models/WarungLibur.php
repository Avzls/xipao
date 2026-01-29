<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarungLibur extends Model
{
    protected $fillable = [
        'warung_id',
        'tanggal',
        'alasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function warung(): BelongsTo
    {
        return $this->belongsTo(Warung::class);
    }
}
