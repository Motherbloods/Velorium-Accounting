<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('tanggal');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('total_tagihan', 18, 2);
            $table->decimal('sisa_piutang', 18, 2);
            $table->enum('status', ['belum_lunas', 'lunas_sebagian', 'lunas'])->default('belum_lunas');
            $table->string('referensi_type', 50)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};