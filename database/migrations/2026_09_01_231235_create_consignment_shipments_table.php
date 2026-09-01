<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_konsinyasi', 50)->unique();
            $table->foreignId('consignee_id')->constrained('consignees');
            $table->date('tanggal_kirim');
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_shipments');
    }
};