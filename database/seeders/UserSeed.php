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
        $users = [
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
            ],
            [
                'name' => 'Igor Matos',
                'email' => 'igormatos@andradeengenhariaeletrica.com.br',
                'password' => Hash::make('password'),
                'phone' => '99999999999',
                'whatsapp' => '999999999999',
                'cpf_cnpj' => '13754674411',
                'birth_date' => '2024-09-05',
                'company_position_id' => 1,
                'sector_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Alana',
                'email' => 'alana@andradeengenhariaeletrica.com.br',
                'password' => Hash::make('password'),
                'phone' => '99999999999',
                'whatsapp' => '999999999999',
                'cpf_cnpj' => '13754674415',
                'birth_date' => '2024-09-05',
                'company_position_id' => 1,
                'sector_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Thiago Andrade',
                'email' => 'suprimentos@andradeengenhariaeletrica.com.br',
                'password' => Hash::make('password'),
                'phone' => '99999999999',
                'whatsapp' => '999999999999',
                'cpf_cnpj' => '13754674419',
                'birth_date' => '2024-09-05',
                'company_position_id' => 1,
                'sector_id' => null,
                'is_active' => true,
            ],                                    
        ];
        User::firstOrCreate();
    }
}
