<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiHarian extends Model
{
    protected $fillable = [
        'warung_id',
        'tanggal',
        'status',
        'dimsum_terjual',
        'cash',
        'modal',
        'omset',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'cash' => 'decimal:2',
        'modal' => 'decimal:2',
        'omset' => 'decimal:2',
    ];

    // Relationships
    public function warung(): BelongsTo
    {
        return $this->belongsTo(Warung::class);
    }

    public function transaksiItems(): HasMany
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function pengeluaranOperasionals(): HasMany
    {
        return $this->warung->pengeluaranOperasionals()
            ->whereDate('tanggal', $this->tanggal);
    }

    // Scopes
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeWarung($query, $warungId)
    {
        return $query->where('warung_id', $warungId);
    }

    public function scopeTanggal($query, $from, $to = null)
    {
        if ($to) {
            return $query->whereBetween('tanggal', [$from, $to]);
        }
        return $query->whereDate('tanggal', $from);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Auto calculate omset
            $model->omset = $model->cash - $model->modal;
        });
    }

    // Accessors
    public function getCashFormattedAttribute()
    {
        return 'Rp ' . number_format($this->cash, 0, ',', '.');
    }

    public function getModalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->modal, 0, ',', '.');
    }

    public function getOmsetFormattedAttribute()
    {
        return 'Rp ' . number_format($this->omset, 0, ',', '.');
    }
}
