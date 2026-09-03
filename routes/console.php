<?php

use App\Console\Commands\RunMonthlyAdjustingEntries;
use App\Console\Commands\RunMonthlyDepreciation;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RunMonthlyDepreciation::class)->monthlyOn(1, '01:00');
Schedule::command(RunMonthlyAdjustingEntries::class)->monthlyOn(1, '01:30');