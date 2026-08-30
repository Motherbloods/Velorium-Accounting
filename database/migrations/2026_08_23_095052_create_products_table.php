<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk', 50)->unique();
            $table->string('nama_produk', 150);
            $table->string('satuan', 20);
            $table->decimal('harga_beli', 18, 2)->default(0);
            $table->decimal('harga_jual', 18, 2)->default(0);
            $table->integer('stok_gudang')->default(0);
            $table->integer('stok_konsinyasi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};