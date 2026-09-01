<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('tanggal');
            $table->enum('tipe', ['tunai', 'kredit']);
            $table->decimal('subtotal', 18, 2);
            $table->decimal('diskon_dagang', 18, 2)->default(0);
            $table->decimal('dpp_ppn', 18, 2);
            $table->decimal('ppn', 18, 2)->default(0);
            $table->decimal('total', 18, 2);
            $table->foreignId('coa_pembayaran_id')->constrained('coa_accounts');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('payable_id')->nullable()->constrained('payables');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};