<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('depreciation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets');
            $table->date('periode');
            $table->decimal('beban_penyusutan', 18, 2);
            $table->decimal('akumulasi_penyusutan_setelah', 18, 2);
            $table->decimal('nilai_buku_setelah', 18, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamps();

            $table->unique(['fixed_asset_id', 'periode'], 'uniq_asset_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_schedules');
    }
};