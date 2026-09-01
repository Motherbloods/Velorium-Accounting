<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->date('periode');
            $table->decimal('saldo_buku', 18, 2);
            $table->decimal('saldo_rekening_koran', 18, 2);
            $table->decimal('saldo_disesuaikan_buku', 18, 2)->nullable();
            $table->decimal('saldo_disesuaikan_bank', 18, 2)->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};