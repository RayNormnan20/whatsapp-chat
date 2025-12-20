<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@example.com';
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => $email,
                'password' => Hash::make('admin12345'),
            ]);
        }
        $user->role = 'admin';
        $user->save();
    }
}

