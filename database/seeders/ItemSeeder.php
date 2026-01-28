<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\StokGudang;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Produk
            ['nama_item' => 'Dimsum', 'satuan' => 'pcs', 'kategori' => 'produk', 'harga_modal' => 2000, 'stok' => 10000, 'min_stock' => 5000],
            
            // Operasional
            ['nama_item' => 'Regulator', 'satuan' => 'unit', 'kategori' => 'operasional', 'harga_modal' => 150000, 'stok' => 20, 'min_stock' => 10],
            ['nama_item' => 'Gas', 'satuan' => 'tabung', 'kategori' => 'operasional', 'harga_modal' => 25000, 'stok' => 50, 'min_stock' => 20],
            
            // Kemasan
            ['nama_item' => 'Mika', 'satuan' => 'pack', 'kategori' => 'kemasan', 'harga_modal' => 35000, 'stok' => 200, 'min_stock' => 100],
            ['nama_item' => 'Plastik', 'satuan' => 'pack', 'kategori' => 'kemasan', 'harga_modal' => 15000, 'stok' => 150, 'min_stock' => 50],
            ['nama_item' => 'Las', 'satuan' => 'roll', 'kategori' => 'kemasan', 'harga_modal' => 8000, 'stok' => 100, 'min_stock' => 30],
        ];

        foreach ($items as $data) {
            $stok = $data['stok'];
            $minStock = $data['min_stock'];
            unset($data['stok'], $data['min_stock']);
            
            $item = Item::create($data);
            
            StokGudang::create([
                'item_id' => $item->id,
                'qty' => $stok,
                'min_stock' => $minStock,
                'last_restock_date' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}
