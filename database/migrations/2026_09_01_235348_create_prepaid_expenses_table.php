<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prepaid_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->foreignId('coa_aset_id')->constrained('coa_accounts');
            $table->foreignId('coa_beban_id')->constrained('coa_accounts');
            $table->date('tanggal_bayar');
            $table->decimal('total_dibayar', 18, 2);
            $table->integer('jumlah_bulan_cakupan');
            $table->decimal('sisa_belum_diakui', 18, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prepaid_expenses');
    }
};