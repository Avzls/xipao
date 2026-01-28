<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistribusiStok extends Model
{
    protected $fillable = [
        'warung_id',
        'item_id',
        'qty_distribusi',
        'tanggal_distribusi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
    ];

    // Relationships
    public function warung(): BelongsTo
    {
        return $this->belongsTo(Warung::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Auto update stock after distribution
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($model) {
            // Reduce warehouse stock
            $stokGudang = StokGudang::where('item_id', $model->item_id)->first();
            if ($stokGudang) {
                $stokGudang->decrement('qty', $model->qty_distribusi);
            }
        });
    }

    // Scopes
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_distribusi', now()->month)
            ->whereYear('tanggal_distribusi', now()->year);
    }
}
