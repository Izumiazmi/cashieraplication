<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- UBAH INI
use Illuminate\Notifications\Notifiable; // <-- TAMBAHKAN INI

class Anggota extends Authenticatable // <-- UBAH INI
{
    use HasFactory, Notifiable; // <-- TAMBAHKAN INI

    protected $table = 'anggotas';

    protected $fillable = [
        'nama_owner',
        'nama_toko',
        'no_hp',
        'alamat',
        'username',
        'password',
        'status',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
