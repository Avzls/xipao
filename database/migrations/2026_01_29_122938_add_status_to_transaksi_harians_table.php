<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_harians', function (Blueprint $table) {
            $table->enum('status', ['buka', 'tutup'])->default('buka')->after('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_harians', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
