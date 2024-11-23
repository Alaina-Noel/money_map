<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Category\CategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Category::query()
            ->where('user_id', auth()->id());

        if (request('month')) {
            $query->where('budget_month', request('month'));
        }

        $categories = $query
            ->with('lineItems')
            ->orderBy('budget_month', 'desc')
            ->orderBy('name')
            ->get();

        // Group by month if no specific month requested
        if (!request('month')) {
            $categories = $categories->groupBy('budget_month');
        }

        return response()->json($categories);
    }

    public function getCurrentMonth(): JsonResponse
    {
        $currentMonth = now()->format('Y-m');

        $categories = Category::where('user_id', auth()->id())
            ->where('budget_month', 'LIKE', $currentMonth . '%')
            ->with('lineItems')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function getByMonth(string $month): JsonResponse
    {
        $categories = Category::where('user_id', auth()->id())
            ->where('budget_month', 'LIKE', $month . '%')
            ->with('lineItems')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $category = Category::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'expected' => $request->expected,
                'actual' => 0,
                'budget_month' => $request->budget_month,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category->load('lineItems')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($category->load('lineItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $category->update([
                'name' => $request->name,
                'expected' => $request->expected,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category->fresh()->load('lineItems')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // This will also delete related line items if you've set up cascade deletes
            $category->delete();

            DB::commit();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
