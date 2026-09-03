<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_period_id')->constrained('fiscal_periods');
            $table->text('konten');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique('fiscal_period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_notes');
    }
};