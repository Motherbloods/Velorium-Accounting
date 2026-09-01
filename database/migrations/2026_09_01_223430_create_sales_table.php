<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('tanggal');
            $table->enum('tipe', ['tunai', 'kredit']);
            $table->decimal('subtotal', 18, 2);
            $table->decimal('diskon_dagang', 18, 2)->default(0);
            $table->decimal('dpp_ppn', 18, 2);
            $table->decimal('ppn', 18, 2)->default(0);
            $table->decimal('total', 18, 2);
            $table->decimal('termin_diskon_persen', 5, 2)->nullable();
            $table->integer('termin_diskon_hari')->nullable();
            $table->integer('termin_jatuh_tempo_hari')->nullable();
            $table->foreignId('coa_pembayaran_id')->constrained('coa_accounts');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('receivable_id')->nullable()->constrained('receivables');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};