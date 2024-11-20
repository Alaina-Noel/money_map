<?php


namespace Database\Factories;

use App\Models\BudgetSummary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetSummaryFactory extends Factory
{
    protected $model = BudgetSummary::class;

    public function definition(): array
    {
        return [
            'user_id'                 => User::factory(),
            'total_expected_income'   => $this->faker->randomFloat(2, 1000, 10000),
            'total_actual_income'     => $this->faker->randomFloat(2, 1000, 10000),
            'total_expected_spending' => $this->faker->randomFloat(2, 1000, 10000),
            'total_actual_spending'   => $this->faker->randomFloat(2, 1000, 10000),
            'leftover'                => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
