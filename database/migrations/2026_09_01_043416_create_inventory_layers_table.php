<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
            $table->date('tanggal_masuk');
            $table->integer('qty_masuk');
            $table->integer('qty_sisa');
            $table->decimal('harga_per_unit', 18, 2);
            $table->string('referensi_type', 50)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_layers');
    }
};