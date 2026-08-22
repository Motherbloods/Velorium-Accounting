<?php

namespace App\Exceptions;

use Exception;

class FiscalPeriodClosedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Periode akuntansi untuk tanggal ini sudah ditutup.');
    }
}