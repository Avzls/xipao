<?php

namespace Database\Seeders;

use App\Models\Warung;
use App\Models\Item;
use App\Models\DistribusiStok;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DistribusiSeeder extends Seeder
{
    public function run(): void
    {
        $warungs = Warung::all();
        $items = Item::all();
        
        // Generate distribution history for past 2 weeks
        for ($day = 14; $day >= 0; $day--) {
            $tanggal = Carbon::now()->subDays($day);
            
            // Not every day has distribution
            if (rand(1, 3) !== 1) continue;
            
            foreach ($warungs->random(rand(1, 3)) as $warung) {
                foreach ($items->random(rand(1, 2)) as $item) {
                    $qty = match($item->kategori) {
                        'produk' => rand(100, 500),
                        'operasional' => rand(1, 5),
                        'kemasan' => rand(5, 20),
                        default => rand(1, 10),
                    };
                    
                    DistribusiStok::create([
                        'warung_id' => $warung->id,
                        'item_id' => $item->id,
                        'qty_distribusi' => $qty,
                        'tanggal_distribusi' => $tanggal,
                        'keterangan' => null,
                    ]);
                }
            }
        }
    }
}
