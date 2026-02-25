<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_harians', function (Blueprint $table) {
            // Add regular index on warung_id first (needed by FK)
            $table->index('warung_id', 'transaksi_harians_warung_id_index');
        });

        Schema::table('transaksi_harians', function (Blueprint $table) {
            // Now safe to drop the unique constraint
            $table->dropUnique('transaksi_harians_warung_id_tanggal_unique');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_harians', function (Blueprint $table) {
            $table->unique(['warung_id', 'tanggal']);
            $table->dropIndex('transaksi_harians_warung_id_index');
        });
    }
};
