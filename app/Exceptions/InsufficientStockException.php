<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $namaProduk, int $tersedia, int $diminta)
    {
        parent::__construct("Stok tidak cukup untuk produk {$namaProduk}: tersedia {$tersedia}, diminta {$diminta}");
    }
}