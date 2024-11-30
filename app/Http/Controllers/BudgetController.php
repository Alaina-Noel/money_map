<?php

namespace App\Http\Controllers;

use App\Http\Requests\Budget\CopyPreviousMonthRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Http\Requests\Budget\CreateBudgetRequest;
use App\Models\Paycheck;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $budgets = Budget::where('user_id', auth()->id())
            ->with(['categories', 'paychecks'])
            ->orderBy('budget_month', 'desc')
            ->get();

        return response()->json($budgets);
    }

    /**
     * @param string $month
     * @return JsonResponse
     */
    public function show(string $month): JsonResponse
    {
        $budget = Budget::where('user_id', auth()->id())
            ->where('budget_month', 'LIKE', $month . '%')
            ->with(['categories', 'categories.lineItems', 'paychecks'])
            ->firstOrFail();

        return response()->json($budget);
    }

    /**
     * @param string $month
     * @return JsonResponse
     */
    public function getDashboardSummary(string $month): JsonResponse
    {
        $user = auth()->user();
        $currentDate = Carbon::parse($month);

        $budget_query = Budget::where('user_id', $user->id)
            ->whereDate('budget_month', $currentDate->format('Y-m-d'))
            ->with(['relatedCategories', 'relatedCategories.lineItems']);

        $budget = $budget_query->first();

        $currentMonthPaychecks = Paycheck::where('user_id', $user->id)
            ->whereYear('pay_date', $currentDate->year)
            ->whereMonth('pay_date', $currentDate->month)
            ->get();

        $currentMonthIncome = $currentMonthPaychecks->sum('amount');

        if (empty($budget)) {
            return response()->json([
                'current_month' => [
                    'expected_income' => $currentMonthIncome,
                    'actual_income' => $currentMonthIncome,
                    'total_budgeted' => 0,
                    'total_spent' => 0,
                    'remaining_budget' => $currentMonthIncome,
                ],
                'spending_by_category' => [],
            ]);
        }

        $currentMonthSpending = $budget->relatedCategories()->get()->sum('actual_amount');
        $currentMonthBudgeted = $budget->relatedCategories()->get()->sum('expected_amount');

        return response()->json([
            'current_month' => [
                'expected_income' => $currentMonthIncome,
                'actual_income' => $currentMonthIncome,
                'total_budgeted' => $currentMonthBudgeted,
                'total_spent' => $currentMonthSpending,
                'remaining_budget' => $currentMonthIncome - $currentMonthSpending,
            ],
            'spending_by_category' => $budget->relatedCategories()->get()->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'expected_amount' => $category->expected_amount,
                'actual_amount' => $category->actual_amount,
                'percentage' => $category->actual_amount > 0 ?
                    ($category->actual_amount / $category->expected_amount) * 100 : 0,
            ]),
        ]);
    }

    /**
     * @param CopyPreviousMonthRequest $request
     * @param string                   $month
     * @return JsonResponse
     */
    public function copyPreviousMonth(CopyPreviousMonthRequest $request, string $month): JsonResponse
    {
        try {
            $user = auth()->user();
            $targetMonth = Carbon::parse($month);

            $budget = Budget::firstOrCreate([
                'user_id' => $user->id,
                'budget_month' => $targetMonth->format('Y-m-d'),
            ]);

            $newCategories = $budget->copyPreviousMonthCategories($targetMonth);

            return response()->json([
                'message' => 'Categories copied successfully',
                'categories' => $newCategories
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to copy categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param CreateBudgetRequest $request
     * @return JsonResponse
     */
    public function store(CreateBudgetRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $budget = Budget::create([
                'user_id' => auth()->id(),
                'budget_month' => $request->budget_month,
                'notes' => $request->notes,
            ]);

            // Copy categories from previous month
            $previousMonth = Carbon::parse($request->budget_month)->subMonth();
            $previousCategories = Category::where('user_id', auth()->id())
                ->where('budget_month', '=', $previousMonth->format('Y-m') . '2024-11-01 00:00:00')
                ->get();

            foreach ($previousCategories as $prevCategory) {
                Category::create([
                    'user_id' => auth()->id(),
                    'name' => $prevCategory->name,
                    'expected_amount' => $prevCategory->expected_amount,
                    'actual_amount' => 0,
                    'budget_month' => $request->budget_month,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Budget created successfully',
                'budget' => $budget->load(['categories', 'paychecks'])
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
