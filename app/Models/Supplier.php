<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use Auditable;

    protected $fillable = [
        'kode_supplier',
        'nama',
        'alamat',
        'telepon',
        'npwp',
    ];
}