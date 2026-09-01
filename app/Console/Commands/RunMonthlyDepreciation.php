<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DepreciationService;
use Illuminate\Console\Command;

class RunMonthlyDepreciation extends Command
{
    protected $signature = 'accounting:run-monthly-depreciation {periode?}';

    protected $description = 'Menjalankan penyusutan bulanan otomatis untuk semua aset tetap aktif';

    public function handle(DepreciationService $depreciationService): int
    {
        $periode = $this->argument('periode') ?? now()->toDateString();
        $user = User::where('role', 'admin')->first();

        if (!$user) {
            $this->error('Tidak ada user admin untuk menjalankan proses ini.');

            return self::FAILURE;
        }

        $results = $depreciationService->runMonthly($periode, $user);

        $this->info(count($results) . ' jadwal penyusutan berhasil dibuat untuk periode ' . date('M Y', strtotime($periode)) . '.');

        return self::SUCCESS;
    }
}