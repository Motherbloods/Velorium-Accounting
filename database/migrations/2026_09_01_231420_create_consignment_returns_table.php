<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('consignment_shipments');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty_retur');
            $table->date('tanggal_retur');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_returns');
    }
};