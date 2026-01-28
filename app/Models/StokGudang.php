<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokGudang extends Model
{
    protected $fillable = [
        'item_id',
        'qty',
        'min_stock',
        'last_restock_date',
    ];

    protected $casts = [
        'last_restock_date' => 'date',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Accessors
    public function getIsLowAttribute()
    {
        return $this->qty < $this->min_stock;
    }

    public function getStatusAttribute()
    {
        if ($this->qty <= 0) return 'habis';
        if ($this->qty < $this->min_stock) return 'menipis';
        return 'aman';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'habis' => 'badge-danger',
            'menipis' => 'badge-warning',
            default => 'badge-success',
        };
    }
}
