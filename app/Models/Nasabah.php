<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    public function Transaction()
    {
        return $this->hasMany(Transaction::class, 'nasabah_id', 'id');
    }
}
