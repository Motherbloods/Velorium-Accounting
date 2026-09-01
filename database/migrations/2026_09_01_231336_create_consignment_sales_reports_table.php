<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('consignment_shipments');
            $table->date('tanggal_lapor');
            $table->integer('total_qty_terjual');
            $table->decimal('total_penjualan', 18, 2);
            $table->decimal('total_hpp', 18, 2);
            $table->decimal('total_komisi', 18, 2);
            $table->decimal('total_diterima', 18, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_sales_reports');
    }
};