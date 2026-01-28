<?php

namespace Database\Seeders;

use App\Models\Warung;
use App\Models\TransaksiHarian;
use App\Models\PengeluaranOperasional;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $warungs = Warung::all();
        $jenispengeluaran = ['gas', 'harian', 'kebersihan', 'listrik'];
        
        // Generate 30 days of transactions
        for ($day = 30; $day >= 0; $day--) {
            $tanggal = Carbon::now()->subDays($day);
            
            foreach ($warungs as $warung) {
                // Skip some days randomly (10% chance)
                if (rand(1, 10) === 1) continue;
                
                $dimsumTerjual = rand(50, 200);
                $cash = $dimsumTerjual * rand(3500, 4500);
                $modal = $dimsumTerjual * rand(1800, 2200);
                
                TransaksiHarian::create([
                    'warung_id' => $warung->id,
                    'tanggal' => $tanggal,
                    'dimsum_terjual' => $dimsumTerjual,
                    'cash' => $cash,
                    'modal' => $modal,
                    'omset' => $cash - $modal,
                    'keterangan' => $day === 0 ? 'Transaksi hari ini' : null,
                ]);
                
                // Add random operational expenses
                if (rand(1, 3) === 1) {
                    PengeluaranOperasional::create([
                        'warung_id' => $warung->id,
                        'tanggal' => $tanggal,
                        'jenis_pengeluaran' => $jenispengeluaran[array_rand($jenispengeluaran)],
                        'nominal' => rand(1, 10) * 5000,
                        'keterangan' => null,
                    ]);
                }
            }
        }
    }
}
