<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coa_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun', 20)->unique();
            $table->string('nama_akun', 150);
            $table->tinyInteger('level');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('tipe_akun', ['aset', 'kewajiban', 'modal', 'pendapatan', 'beban']);
            $table->enum('saldo_normal', ['debit', 'kredit']);
            $table->boolean('is_postable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('coa_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coa_accounts');
    }
};