<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bukti', 50)->unique();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->foreignId('coa_kas_bank_id')->constrained('coa_accounts');
            $table->foreignId('coa_lawan_id')->constrained('coa_accounts');
            $table->decimal('jumlah', 18, 2);
            $table->string('keterangan', 255)->nullable();
            $table->string('referensi_type', 50)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};