<?php

namespace App\Services;

use App\Models\DocumentNumberSequence;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public function generate(string $prefix, ?string $tanggal = null): string
    {
        $tanggal = $tanggal ? date('Y-m-d', strtotime($tanggal)) : now()->toDateString();

        return DB::transaction(function () use ($prefix, $tanggal) {
            $sequence = DocumentNumberSequence::where('prefix', $prefix)
                ->where('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = DocumentNumberSequence::create([
                    'prefix' => $prefix,
                    'tanggal' => $tanggal,
                    'urutan_terakhir' => 0,
                ]);
            }

            $sequence->urutan_terakhir += 1;
            $sequence->save();

            $tanggalFormat = date('Ymd', strtotime($tanggal));
            $urutan = str_pad((string) $sequence->urutan_terakhir, 4, '0', STR_PAD_LEFT);

            return "{$prefix}-{$tanggalFormat}-{$urutan}";
        });
    }
}