<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdjustingEntryService;
use Illuminate\Console\Command;

class RunMonthlyAdjustingEntries extends Command
{
    protected $signature = 'accounting:run-monthly-adjusting-entries {periode?}';

    protected $description = 'Menjalankan jurnal penyesuaian bulanan otomatis (biaya dibayar dimuka & pendapatan diterima dimuka)';

    public function handle(AdjustingEntryService $adjustingEntryService): int
    {
        $periode = $this->argument('periode') ?? now()->toDateString();
        $user = User::where('role', 'admin')->first();

        if (!$user) {
            $this->error('Tidak ada user admin untuk menjalankan proses ini.');

            return self::FAILURE;
        }

        $results = $adjustingEntryService->runMonthly($periode, $user);

        $this->info(count($results) . ' jurnal penyesuaian berhasil dibuat untuk periode ' . date('M Y', strtotime($periode)) . '.');

        return self::SUCCESS;
    }
}