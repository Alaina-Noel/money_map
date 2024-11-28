<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LineItem;
use App\Models\User;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialBudgetSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Get the latest user if no specific user is provided
            $user = User::findOrFail(1); // Your existing user

            if (!$user) {
                $this->command->error('No users found in the database. Please create a user first.');
                return;
            }

            $currentMonth = Carbon::now()->startOfMonth();

            // Create or get budget for the month
            $budget = Budget::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'budget_month' => $currentMonth,
                ],
                [
                    'notes' => 'Initial budget for ' . $currentMonth->format('F Y'),
                ]
            );

            // Check if categories already exist for this month and user
            $existingCategories = Category::where('user_id', $user->id)
                ->where('budget_month', $currentMonth)
                ->exists();

            if ($existingCategories) {
                $this->command->info('Categories already exist for this month. Skipping seeding.');
                return;
            }

            $categories = [
                [
                    'name' => 'Fixed Bills',
                    'expected_amount' => 2722.40,
                    'sample_items' => [
                        [
                            'description' => 'Rent',
                            'amount' => 1300.00,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Monthly rent payment'
                        ],
                        [
                            'description' => 'Utilities',
                            'amount' => 49.00,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Electric bill'
                        ],
                    ]
                ],
                [
                    'name' => 'Restaurants',
                    'expected_amount' => 360.00,
                    'sample_items' => [
                        [
                            'description' => 'Chipotle',
                            'amount' => 15.75,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Lunch'
                        ],
                    ]
                ],
                [
                    'name' => 'Groceries',
                    'expected_amount' => 370.00,
                    'sample_items' => [
                        [
                            'description' => 'Whole Foods',
                            'amount' => 14.56,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Weekly groceries'
                        ],
                    ]
                ],
                [
                    'name' => 'Transportation',
                    'expected_amount' => 115.00,
                    'sample_items' => [
                        [
                            'description' => 'Subway Rides',
                            'amount' => 115.00,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Monthly pass'
                        ],
                    ]
                ],
                [
                    'name' => 'Nails & Hair',
                    'expected_amount' => 68.00,
                    'sample_items' => [
                        [
                            'description' => 'Manicure',
                            'amount' => 68.00,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Regular maintenance'
                        ],
                    ]
                ],
                [
                    'name' => 'Health & Therapy',
                    'expected_amount' => 400.00,
                    'sample_items' => [
                        [
                            'description' => 'Therapy Session',
                            'amount' => 145.00,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Weekly session'
                        ],
                    ]
                ],
                [
                    'name' => 'Amazon',
                    'expected_amount' => 170.00,
                    'sample_items' => [
                        [
                            'description' => 'Household Supplies',
                            'amount' => 45.67,
                            'date' => $currentMonth->copy(),
                            'notes' => 'Monthly supplies'
                        ],
                    ]
                ],
                [
                    'name' => 'Unplanned',
                    'expected_amount' => 375.00,
                    'sample_items' => []
                ],
            ];

            $this->command->info('Creating categories for ' . $currentMonth->format('F Y'));

            foreach ($categories as $categoryData) {
                $category = Category::create([
                    'user_id' => $user->id,
                    'name' => $categoryData['name'],
                    'expected_amount' => $categoryData['expected_amount'],
                    'actual_amount' => 0,
                    'budget_month' => $currentMonth,
                ]);

                foreach ($categoryData['sample_items'] as $itemData) {
                    LineItem::create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'description' => $itemData['description'],
                        'amount' => $itemData['amount'],
                        'date' => $itemData['date'], // Now using different dates throughout the month
                        'notes' => $itemData['notes']
                    ]);

                    $category->actual_amount += $itemData['amount'];
                }

                if ($category->actual_amount > 0) {
                    $category->save();
                }
            }

            DB::commit();
            $this->command->info('Successfully created ' . count($categories) . ' categories for ' . $currentMonth->format('F Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding data: ' . $e->getMessage());
            throw $e;
        }
    }
}
