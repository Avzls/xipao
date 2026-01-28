<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warung extends Model
{
    protected $fillable = [
        'nama_warung',
        'alamat',
        'status',
    ];

    // Relationships
    public function transaksiHarians(): HasMany
    {
        return $this->hasMany(TransaksiHarian::class);
    }

    public function distribusiStoks(): HasMany
    {
        return $this->hasMany(DistribusiStok::class);
    }

    public function pengeluaranOperasionals(): HasMany
    {
        return $this->hasMany(PengeluaranOperasional::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Accessors
    public function getOmsetHariIniAttribute()
    {
        return $this->transaksiHarians()
            ->whereDate('tanggal', today())
            ->sum('omset');
    }

    public function getOmsetBulanIniAttribute()
    {
        return $this->transaksiHarians()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('omset');
    }
}
