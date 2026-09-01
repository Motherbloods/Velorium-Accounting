<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained('bank_reconciliations');
            $table->enum('kategori', ['sisi_buku', 'sisi_bank']);
            $table->string('jenis', 100);
            $table->string('keterangan', 255)->nullable();
            $table->decimal('jumlah', 18, 2);
            $table->boolean('sudah_diposting')->default(false);
            $table->foreignId('cash_transaction_id')->nullable()->constrained('cash_transactions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_items');
    }
};