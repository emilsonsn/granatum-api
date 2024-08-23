<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::createOrFirst([
            'name' => 'Admin',
            'email' => 'admin@admin',
            'password' => Hash::make('admin'),
            'is_active' => true,
            'is_admin' => true
        ]);
    }
}
