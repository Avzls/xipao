<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WarungSeeder::class,
            ItemSeeder::class,
            TransaksiSeeder::class,
            DistribusiSeeder::class,
        ]);
    }
}
