<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use Auditable;

    protected $fillable = [
        'kode_customer',
        'nama',
        'alamat',
        'telepon',
        'npwp',
    ];
}