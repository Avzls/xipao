<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    protected $fillable = [
        'nama_item',
        'satuan',
        'kategori',
        'harga',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // Relationships
    public function stokGudang(): HasOne
    {
        return $this->hasOne(StokGudang::class);
    }

    // Accessors
    public function getStokAttribute()
    {
        return $this->stokGudang?->qty ?? 0;
    }
}
