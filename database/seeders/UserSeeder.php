<?php

namespace Database\Seeders;

use App\Enums\RoleUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@central.local',
            'password' => 'password',
            'role' => RoleUsuario::Admin,
        ]);
    }
}
