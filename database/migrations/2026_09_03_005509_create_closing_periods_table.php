<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('closing_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_period_id')->constrained('fiscal_periods');
            $table->decimal('laba_rugi_bersih', 18, 2)->nullable();
            $table->foreignId('closing_journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('reversing_journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_periods');
    }
};