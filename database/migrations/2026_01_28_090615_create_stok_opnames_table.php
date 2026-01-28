<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_opname');
            $table->integer('qty_sistem')->default(0);
            $table->integer('qty_fisik')->default(0);
            $table->integer('selisih')->default(0);
            $table->enum('status', ['sesuai', 'kurang', 'lebih'])->default('sesuai');
            $table->text('keterangan')->nullable();
            $table->boolean('is_adjusted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opnames');
    }
};
