<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bukti', 50)->unique();
            $table->date('tanggal');
            $table->string('keterangan', 255)->nullable();
            $table->string('referensi_type', 50)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->foreignId('fiscal_period_id')->constrained('fiscal_periods');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'posted', 'rejected'])->default('draft');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('catatan_penolakan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};