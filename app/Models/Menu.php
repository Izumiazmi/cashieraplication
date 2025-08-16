<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'jenis',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'menu_id');
    }
}
