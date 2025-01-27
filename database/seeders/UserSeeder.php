<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create First Owner
        User::create([
            'name' => 'Fahluzi Ahmad',
            'email' => 'fahluzi@bubutbali.id',
            'password' => Hash::make('ownerbubutbali'),
            'role' => 'owner'
        ]);

        // Create Second Owner
        User::create([
            'name' => 'Rifki Nur Ikhwan',
            'email' => 'rfkinrikhwan@bubutbali.id',
            'password' => Hash::make('ownerbubutbali'),
            'role' => 'owner'
        ]);

        // Create Kasir
        User::create([
            'name' => 'Kasir Bubut Bali',
            'email' => 'kasir@bubutbali.id',
            'password' => Hash::make('kasirbubutbali'),
            'role' => 'kasir'
        ]);
    }
}
