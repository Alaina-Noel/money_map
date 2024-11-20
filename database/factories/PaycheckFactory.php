<?php


namespace Database\Factories;

use App\Models\Paycheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaycheckFactory extends Factory
{
    protected $model = Paycheck::class;

    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'pay_date' => $this->faker->date,
            'amount'   => $this->faker->randomFloat(2, 1000, 5000),
        ];
    }
}
