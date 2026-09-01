<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('consignment_shipments');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty_kirim');
            $table->integer('qty_terjual')->default(0);
            $table->integer('qty_retur')->default(0);
            $table->decimal('harga_titip', 18, 2);
            $table->decimal('hpp_satuan', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_shipment_items');
    }
};