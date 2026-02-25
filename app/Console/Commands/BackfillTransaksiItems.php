<?php

namespace App\Console\Commands;

use App\Models\TransaksiHarian;
use App\Models\TransaksiItem;
use App\Models\Item;
use Illuminate\Console\Command;

class BackfillTransaksiItems extends Command
{
    protected $signature = 'transaksi:backfill';
    protected $description = 'Backfill transaksi_items from dimsum_terjual for old transactions';

    public function handle()
    {
        // Find the Dimsum item
        $dimsum = Item::where('nama_item', 'like', '%Dimsum%')
            ->where('nama_item', 'not like', '%goreng%')
            ->where('nama_item', 'not like', '%keju%')
            ->first();

        if (!$dimsum) {
            $this->error('Item Dimsum tidak ditemukan!');
            return 1;
        }

        $this->info("Menggunakan item: {$dimsum->nama_item} (ID: {$dimsum->id}, Harga: {$dimsum->harga})");

        // Find transactions with dimsum_terjual > 0 but no transaksi_items
        $transaksis = TransaksiHarian::where('dimsum_terjual', '>', 0)
            ->whereDoesntHave('transaksiItems')
            ->where('status', 'buka')
            ->get();

        $this->info("Ditemukan {$transaksis->count()} transaksi lama yang perlu di-backfill.");

        if ($transaksis->isEmpty()) {
            $this->info('Tidak ada data yang perlu di-backfill.');
            return 0;
        }

        $bar = $this->output->createProgressBar($transaksis->count());
        $count = 0;

        foreach ($transaksis as $tx) {
            TransaksiItem::create([
                'transaksi_harian_id' => $tx->id,
                'item_id' => $dimsum->id,
                'qty' => $tx->dimsum_terjual,
                'harga' => $dimsum->harga,
            ]);
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Berhasil backfill {$count} transaksi!");

        return 0;
    }
}
