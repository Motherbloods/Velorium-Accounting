<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adjusting_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['prepaid_expense', 'unearned_revenue', 'accrued_expense', 'accrued_revenue']);
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->date('periode');
            $table->decimal('jumlah', 18, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();

            $table->unique(['tipe', 'referensi_id', 'periode'], 'uniq_tipe_referensi_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjusting_entries');
    }
};