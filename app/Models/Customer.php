<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'kode_customer',
        'nama',
        'alamat',
        'telepon',
        'npwp',
    ];
}