<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_opnames', function (Blueprint $table) {
            $table->decimal('expected_cash', 15, 2)->default(0)->after('selisih');
            $table->decimal('actual_cash', 15, 2)->nullable()->after('expected_cash');
            $table->decimal('cash_selisih', 15, 2)->default(0)->after('actual_cash');
        });
    }

    public function down(): void
    {
        Schema::table('stok_opnames', function (Blueprint $table) {
            $table->dropColumn(['expected_cash', 'actual_cash', 'cash_selisih']);
        });
    }
};
