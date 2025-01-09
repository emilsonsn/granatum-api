<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetGenerated>
 */
class BudgetGeneratedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\BudgetGenerated::class;

    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
            'budget_id' => Budget::factory(), // Relacionamento com o modelo Budget
            'lead_id' => Lead::factory(),     // Relacionamento com o modelo Lead
            'status' => $this->faker->randomElement(['Generated', 'Delivered', 'Approved', 'Desapproved']),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
