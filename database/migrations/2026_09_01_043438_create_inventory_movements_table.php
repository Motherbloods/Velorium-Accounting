<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('qty');
            $table->decimal('harga_per_unit', 18, 2);
            $table->string('referensi_type', 50)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};