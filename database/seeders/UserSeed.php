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

        User::where('email', 'admin@admin')->delete();
        
        User::firstOrCreate([
            'email' => 'admin@admin',
        ],
        [
            'name' => 'Admin',
            'email' => 'admin@admin',
            'password' => Hash::make('admin'),
            'phone' => '83991236636',
            'whatsapp' => '83991236636',
            'cpf_cnpj' => '13754674412',
            'birth_date' => '2001-12-18',
            'company_position_id' => 1,
            'sector_id' => null,
            'is_active' => true,
        ]);

        User::firstOrCreate([
            'email' => 'user@user',
        ],
        [
            'name' => 'User',
            'email' => 'user@user',
            'password' => Hash::make('user'),
            'phone' => '8399999999',
            'whatsapp' => '8399999999',
            'cpf_cnpj' => '13754674413',
            'birth_date' => '2001-12-18',
            'company_position_id' => 4,
            'sector_id' => null,
            'is_active' => true,
        ]);
    }
}
