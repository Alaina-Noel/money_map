<?php

namespace App\Http\Controllers;

use App\Models\BudgetSummary;
use App\Models\Category;
use App\Http\Requests\Budget\CreateBudgetRequest;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetSummaryController extends Controller
{
    public function index(): JsonResponse
    {
        $budgets = BudgetSummary::where('user_id', auth()->id())
            ->with(['categories', 'categories.lineItems'])
            ->orderBy('budget_month', 'desc')
            ->get();

        return response()->json($budgets);
    }

    public function store(CreateBudgetRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $budgetSummary = BudgetSummary::create([
                'user_id' => auth()->id(),
                'budget_month' => $validated['budget_month'],
                'expected_income' => $validated['expected_income'],
                'actual_income' => $validated['actual_income'] ?? 0,
            ]);

            // Copy categories from previous month logic...
            $previousMonth = Carbon::parse($validated['budget_month'])->subMonth();
            $previousCategories = Category::where('user_id', auth()->id())
                ->where('budget_month', $previousMonth)
                ->get();

            foreach ($previousCategories as $prevCategory) {
                Category::create([
                    'user_id' => auth()->id(),
                    'name' => $prevCategory->name,
                    'expected' => $prevCategory->expected,
                    'actual' => 0,
                    'budget_month' => $validated['budget_month'],
                ]);
            }


            return response()->json([
                'message' => 'Budget created successfully',
                'budget' => $budgetSummary->load('categories')
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack(); //says undefined class DB
            return response()->json([
                'message' => 'Failed to create budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(BudgetSummary $budgetSummary): JsonResponse
    {
        $budgetSummary->load([
            'categories',
            'categories.lineItems',
            'paychecks' => function ($query) use ($budgetSummary) {
                $query->whereMonth('pay_date', Carbon::parse($budgetSummary->budget_month)->month)
                    ->whereYear('pay_date', Carbon::parse($budgetSummary->budget_month)->year);
            }
        ]);

        $totalSpent = $budgetSummary->categories->sum('actual');
        $totalBudgeted = $budgetSummary->categories->sum('expected');
        $actualIncome = $budgetSummary->paychecks->sum('amount');

        return response()->json([
            'budget' => $budgetSummary,
            'summary' => [
                'total_spent' => $totalSpent,
                'total_budgeted' => $totalBudgeted,
                'total_remaining' => $budgetSummary->expected_income - $totalSpent,
                'actual_income' => $actualIncome,
            ]
        ]);
    }
}
