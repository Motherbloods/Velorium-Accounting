<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentNumberSequence extends Model
{
    protected $fillable = [
        'prefix',
        'tanggal',
        'urutan_terakhir',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}