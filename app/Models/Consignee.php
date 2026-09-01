<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignee extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'persentase_komisi',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(ConsignmentShipment::class, 'consignee_id');
    }
}