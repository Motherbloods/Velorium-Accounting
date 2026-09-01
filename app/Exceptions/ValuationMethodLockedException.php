<?php

namespace App\Exceptions;

use Exception;

class ValuationMethodLockedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Metode penilaian tidak dapat diubah karena produk sudah memiliki transaksi persediaan.');
    }
}