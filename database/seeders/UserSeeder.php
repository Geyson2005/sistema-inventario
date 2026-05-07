<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Administrador
        User::create([
            'name' => 'Julio P',
            'email' => 'jucep_26@outlook.com',
            'password' => Hash::make('admin3192'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Usuario Operador
        User::create([
            'name' => 'Jhonatan',
            'email' => 'jhonatanpaulinisilva4@gmail.com',
            'password' => Hash::make('operador3192'),
            'role' => 'operator',
            'status' => 'active',
        ]);


    }
}