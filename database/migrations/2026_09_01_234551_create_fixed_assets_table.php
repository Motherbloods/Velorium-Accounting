<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset', 50)->unique();
            $table->string('nama_aset', 150);
            $table->foreignId('coa_aset_id')->constrained('coa_accounts');
            $table->foreignId('coa_akumulasi_penyusutan_id')->constrained('coa_accounts');
            $table->date('tanggal_perolehan');
            $table->decimal('harga_perolehan', 18, 2);
            $table->decimal('nilai_residu', 18, 2)->default(0);
            $table->integer('umur_manfaat_tahun');
            $table->integer('umur_manfaat_fiskal_tahun')->nullable();
            $table->enum('metode_penyusutan', ['garis_lurus', 'saldo_menurun_ganda']);
            $table->decimal('akumulasi_penyusutan', 18, 2)->default(0);
            $table->decimal('nilai_buku', 18, 2);
            $table->enum('status', ['aktif', 'dilepas'])->default('aktif');
            $table->date('tanggal_pelepasan')->nullable();
            $table->decimal('harga_jual_pelepasan', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};