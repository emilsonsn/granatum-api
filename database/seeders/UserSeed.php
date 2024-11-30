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
        User::firstOrCreate([
            'email' => 'admin@admin',
        ],
        [
            'name' => 'Administrador',
            'email' => 'admin@admin',
            'password' => Hash::make('admin'),
            'phone' => '99999999999',
            'whatsapp' => '999999999999',
            'cpf_cnpj' => '13754674412',
            'birth_date' => '2024-09-05',
            'company_position_id' => 1,
            'sector_id' => null,
            'is_active' => true,
        ]);
    }
}
