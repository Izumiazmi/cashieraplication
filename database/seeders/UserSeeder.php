<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- Import Hash facade
use App\Models\User;                   // <-- Import model User

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => env('ADMIN_USERNAME', 'Admin Default'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);
    }
}
