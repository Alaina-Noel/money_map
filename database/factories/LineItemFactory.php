<?php


namespace Database\Factories;

use App\Models\LineItem;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineItemFactory extends Factory
{
    protected $model = LineItem::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence,
            'amount'      => $this->faker->randomFloat(2, 1, 500),
        ];
    }
}
