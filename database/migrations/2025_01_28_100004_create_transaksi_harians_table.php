<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warung_id')->constrained('warungs')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('dimsum_terjual')->default(0);
            $table->decimal('cash', 15, 2)->default(0);
            $table->decimal('modal', 15, 2)->default(0);
            $table->decimal('omset', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Unique constraint: satu warung, satu tanggal hanya boleh satu transaksi
            $table->unique(['warung_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_harians');
    }
};
