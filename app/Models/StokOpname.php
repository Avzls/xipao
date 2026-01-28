<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpname extends Model
{
    protected $fillable = [
        'item_id',
        'tanggal_opname',
        'qty_sistem',
        'qty_fisik',
        'selisih',
        'status',
        'keterangan',
        'is_adjusted',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
        'is_adjusted' => 'boolean',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Boot method for auto-calculate
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->selisih = $model->qty_fisik - $model->qty_sistem;
            
            if ($model->selisih == 0) {
                $model->status = 'sesuai';
            } elseif ($model->selisih < 0) {
                $model->status = 'kurang';
            } else {
                $model->status = 'lebih';
            }
        });
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'sesuai' => 'badge-success',
            'kurang' => 'badge-danger',
            'lebih' => 'badge-warning',
            default => 'badge-info',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'sesuai' => '✅ Sesuai',
            'kurang' => '❌ Kurang ' . abs($this->selisih),
            'lebih' => '⚠️ Lebih ' . $this->selisih,
            default => '-',
        };
    }
}
