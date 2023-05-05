<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function Nasabah()
    {
        return $this->hasOne(Nasabah::class, 'id', 'nasabah_id');
    }
}
