<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payable_id')->constrained('payables');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_pokok', 18, 2);
            $table->decimal('jumlah_bunga', 18, 2)->default(0);
            $table->foreignId('coa_kas_bank_id')->constrained('coa_accounts');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payable_payments');
    }
};