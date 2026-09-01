<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_sales_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_sales_report_id')->constrained('consignment_sales_reports');
            $table->foreignId('shipment_item_id')->constrained('consignment_shipment_items');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty_terjual');
            $table->decimal('harga_titip', 18, 2);
            $table->decimal('hpp_satuan', 18, 2);
            $table->decimal('subtotal_penjualan', 18, 2);
            $table->decimal('subtotal_hpp', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_sales_report_items');
    }
};