<?php
namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'name'     => $this->faker->word,
            'expected' => $this->faker->randomFloat(2, 10, 1000),
            'actual'   => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
