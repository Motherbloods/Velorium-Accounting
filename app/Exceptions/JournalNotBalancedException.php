<?php

namespace App\Exceptions;

use Exception;

class JournalNotBalancedException extends Exception
{
    public function __construct(string $totalDebit, string $totalKredit)
    {
        parent::__construct("Jurnal tidak balance: total debit {$totalDebit} tidak sama dengan total kredit {$totalKredit}");
    }
}