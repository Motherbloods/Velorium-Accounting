<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pph_final_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('periode_pajak', 7)->unique();
            $table->decimal('omzet_bruto', 18, 2);
            $table->decimal('tarif_persen', 5, 2);
            $table->decimal('jumlah_pajak', 18, 2);
            $table->enum('status', ['diakui', 'disetor'])->default('diakui');
            $table->foreignId('journal_entry_pengakuan_id')->nullable()->constrained('journal_entries');
            $table->foreignId('journal_entry_penyetoran_id')->nullable()->constrained('journal_entries');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pph_final_transactions');
    }
};  