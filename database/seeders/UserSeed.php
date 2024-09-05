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
            'email' => 'igormatos@andradeengenhariaeletrica.com.br',
        ],
        [
            'name' => 'Igor Matos',
            'email' => 'igormatos@andradeengenhariaeletrica.com.br',
            'password' => Hash::make('@123Mudar'),
            'phone' => '99999999999',
            'whatsapp' => '999999999999',
            'cpf_cnpj' => '12345678910',
            'birth_date' => '2024-09-05',
            'company_position_id' => 1,
            'sector_id' => null,
            'is_active' => true,
        ]);        

        User::firstOrCreate([
            'email' => 'alana@andradeengenharia.com.br',
        ],
        [
            'name' => 'Alana Andrade',
            'email' => 'alana@andradeengenharia.com.br',
            'password' => Hash::make('@123Mudar'),
            'phone' => '99999999999',
            'whatsapp' => '999999999999',
            'cpf_cnpj' => '12345678911',
            'birth_date' => '2024-09-05',
            'company_position_id' => 2,
            'sector_id' => null,
            'is_active' => true,
        ]);

        User::firstOrCreate([
            'email' => 'suprimentos@andradeengenhariaeletrica.com.br',
        ],
        [
            'name' => 'Tiago Andrade',
            'email' => 'suprimentos@andradeengenhariaeletrica.com.br',
            'password' => Hash::make('@123Mudar'),
            'phone' => '99999999999',
            'whatsapp' => '999999999999',
            'cpf_cnpj' => '12345678912',
            'birth_date' => '2024-09-05',
            'company_position_id' => 3,
            'sector_id' => null,
            'is_active' => true,
        ]);
    }
}
