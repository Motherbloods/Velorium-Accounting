<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('metode_penilaian', ['fifo', 'rata_rata'])->default('rata_rata')->after('satuan');
            $table->decimal('harga_rata_rata', 18, 2)->default(0)->after('metode_penilaian');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['metode_penilaian', 'harga_rata_rata']);
        });
    }
};