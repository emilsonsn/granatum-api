<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = \App\Models\User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // Senha padrão (alterar se necessário)
            'phone' => $this->faker->phoneNumber,
            'whatsapp' => $this->faker->boolean ? $this->faker->phoneNumber : null,
            'cpf_cnpj' => $this->faker->numerify('###########'), // CPF/CNPJ fictício
            'birth_date' => $this->faker->date('Y-m-d'),
            'company_position_id' => null, // Alterar se necessário ou criar uma `CompanyPositionFactory`
            'sector_id' => null, // Alterar se necessário ou criar uma `SectorFactory`
            'is_active' => $this->faker->boolean,
            'photo' => $this->faker->imageUrl(100, 100, 'people', true, 'User Photo'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
