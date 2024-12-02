<?php


namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Store a new category and associate it with the current month's budget
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'expected_amount' => 'required|numeric|min:0',
            'budget_month'    => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $budget = Budget::firstOrCreate([
                'user_id'      => Auth::id(),
                'budget_month' => $request->budget_month,
            ]);

            $category = new Category([
                'name'            => $request->name,
                'expected_amount' => $request->expected_amount,
                'user_id'         => Auth::id(),
            ]);

            // Save the category through the budget relationship
            $budget->relatedCategories()->save($category);

            return response()->json([
                'message'  => 'Category created successfully',
                'category' => $category
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create category',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized access to category'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'expected_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update([
                'name' => $request->name,
                'expected_amount' => $request->expected_amount,
            ]);

            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified category
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        // Check if the category belongs to the authenticated user
        if ($category->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized access to category'
            ], 403);
        }

        try {
            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
