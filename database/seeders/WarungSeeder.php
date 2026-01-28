<?php

namespace Database\Seeders;

use App\Models\Warung;
use Illuminate\Database\Seeder;

class WarungSeeder extends Seeder
{
    public function run(): void
    {
        $warungs = [
            ['nama_warung' => 'Warung A', 'alamat' => 'Jl. Merdeka No. 1, Jakarta', 'status' => 'aktif'],
            ['nama_warung' => 'Warung B', 'alamat' => 'Jl. Sudirman No. 15, Jakarta', 'status' => 'aktif'],
            ['nama_warung' => 'Warung C', 'alamat' => 'Jl. Gatot Subroto No. 20, Jakarta', 'status' => 'aktif'],
            ['nama_warung' => 'Warung D', 'alamat' => 'Jl. Thamrin No. 8, Jakarta', 'status' => 'aktif'],
            ['nama_warung' => 'Warung E', 'alamat' => 'Jl. Rasuna Said No. 12, Jakarta', 'status' => 'aktif'],
        ];

        foreach ($warungs as $warung) {
            Warung::create($warung);
        }
    }
}
