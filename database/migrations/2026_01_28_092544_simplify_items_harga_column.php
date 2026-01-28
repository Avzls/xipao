<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add new harga column
            $table->decimal('harga', 15, 2)->default(0)->after('kategori');
        });

        // Copy harga_jual to harga (if exists)
        \DB::statement('UPDATE items SET harga = COALESCE(harga_jual, harga_modal, 0)');

        Schema::table('items', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('items', 'harga_modal')) {
                $table->dropColumn('harga_modal');
            }
            if (Schema::hasColumn('items', 'harga_jual')) {
                $table->dropColumn('harga_jual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
        });

        \DB::statement('UPDATE items SET harga_jual = harga, harga_modal = harga');

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};
