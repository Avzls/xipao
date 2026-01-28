<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestokGudang extends Model
{
    protected $fillable = [
        'item_id',
        'qty_masuk',
        'tanggal_masuk',
        'harga_beli',
        'supplier',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'harga_beli' => 'decimal:2',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Auto update stock after restock
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($model) {
            // Increase warehouse stock
            $stokGudang = StokGudang::firstOrCreate(
                ['item_id' => $model->item_id],
                ['qty' => 0, 'min_stock' => 0]
            );
            $stokGudang->increment('qty', $model->qty_masuk);
            $stokGudang->update(['last_restock_date' => $model->tanggal_masuk]);
        });
    }

    // Accessors
    public function getHargaBeliFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getTotalAttribute()
    {
        return $this->qty_masuk * $this->harga_beli;
    }

    public function getTotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
