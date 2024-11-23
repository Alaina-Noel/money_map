<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LineItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::findOrFail(1); // Your existing user
        $currentMonth = Carbon::now()->startOfMonth();

        // Core categories
        $categories = [
            [
                'name' => 'Fixed Bills',
                'expected' => 2722.40,
                'sample_items' => [
                    ['description' => 'Rent', 'amount' => 1300.00],
                    ['description' => 'Utilities', 'amount' => 49.00],
                ]
            ],
            [
                'name' => 'Restaurants',
                'expected' => 360.00,
                'sample_items' => [
                    ['description' => 'Chipotle', 'amount' => 15.75],
                ]
            ],
            [
                'name' => 'Groceries',
                'expected' => 370.00,
                'sample_items' => [
                    ['description' => 'Whole Foods', 'amount' => 14.56],
                ]
            ],
            [
                'name' => 'Transportation',
                'expected' => 115.00,
                'sample_items' => [
                    ['description' => 'Subway Rides', 'amount' => 115.00],
                ]
            ],
            [
                'name' => 'Nails & Hair',
                'expected' => 68.00,
                'sample_items' => [
                    ['description' => 'Manicure', 'amount' => 68.00],
                ]
            ],
            [
                'name' => 'Health & Therapy',
                'expected' => 400.00,
                'sample_items' => [
                    ['description' => 'Therapy Session', 'amount' => 145.00],
                ]
            ],
            [
                'name' => 'Amazon',
                'expected' => 170.00,
                'sample_items' => [
                    ['description' => 'Household Supplies', 'amount' => 45.67],
                ]
            ],
            [
                'name' => 'Unplanned',
                'expected' => 375.00,
                'sample_items' => []
            ],
        ];

        // Create categories and their sample items
        foreach ($categories as $categoryData) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => $categoryData['name'],
                'expected' => $categoryData['expected'],
                'actual' => 0,
                'budget_month' => $currentMonth,
            ]);

            // Create sample line items for this category
            foreach ($categoryData['sample_items'] as $itemData) {
                LineItem::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'budget_month' => $currentMonth,
                ]);
            }
        }
    }
}
