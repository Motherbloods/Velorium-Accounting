<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unearned_revenues', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->foreignId('coa_kewajiban_id')->constrained('coa_accounts');
            $table->foreignId('coa_pendapatan_id')->constrained('coa_accounts');
            $table->date('tanggal_terima');
            $table->decimal('total_diterima', 18, 2);
            $table->integer('jumlah_bulan_cakupan');
            $table->decimal('sisa_belum_diakui', 18, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unearned_revenues');
    }
};