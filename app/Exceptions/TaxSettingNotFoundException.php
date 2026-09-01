<?php

namespace App\Exceptions;

use Exception;

class TaxSettingNotFoundException extends Exception
{
    public function __construct(string $namaPajak)
    {
        parent::__construct("Tarif pajak '{$namaPajak}' belum diatur atau belum berlaku pada tanggal ini.");
    }
}