<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->decimal('termin_diskon_persen', 5, 2)->nullable()->after('tanggal_jatuh_tempo');
            $table->integer('termin_diskon_hari')->nullable()->after('termin_diskon_persen');
        });

        Schema::table('payables', function (Blueprint $table) {
            $table->decimal('termin_diskon_persen', 5, 2)->nullable()->after('tanggal_jatuh_tempo');
            $table->integer('termin_diskon_hari')->nullable()->after('termin_diskon_persen');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('termin_diskon_persen', 5, 2)->nullable()->after('diskon_dagang');
            $table->integer('termin_diskon_hari')->nullable()->after('termin_diskon_persen');
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn(['termin_diskon_persen', 'termin_diskon_hari']);
        });

        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn(['termin_diskon_persen', 'termin_diskon_hari']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['termin_diskon_persen', 'termin_diskon_hari']);
        });
    }
};