<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tax_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('referensi_type', 50);
            $table->unsignedBigInteger('referensi_id');
            $table->enum('tipe', ['ppn_keluaran', 'ppn_masukan']);
            $table->decimal('dpp', 18, 2);
            $table->decimal('tarif_persen', 5, 2);
            $table->decimal('jumlah_pajak', 18, 2);
            $table->string('periode_pajak', 7);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_transactions');
    }
};