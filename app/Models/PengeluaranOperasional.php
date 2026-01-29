<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengeluaranOperasional extends Model
{
    protected $fillable = [
        'warung_id',
        'tanggal',
        'jenis_pengeluaran',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    // Relationships
    public function warung(): BelongsTo
    {
        return $this->belongsTo(Warung::class);
    }

    // Constants for jenis_pengeluaran
    const JENIS = [
        'gaji' => 'Gaji',
        'gas' => 'Gas',
        'harian' => 'Harian',
        'kebersihan' => 'Kebersihan',
        'lainnya' => 'Lainnya',
        'las' => 'Las',
        'listrik' => 'Listrik',
        'mika' => 'Mika',
        'plastik' => 'Plastik',
        'regulator' => 'Regulator',
    ];

    // Accessors
    public function getNominalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis_pengeluaran] ?? $this->jenis_pengeluaran;
    }
}
