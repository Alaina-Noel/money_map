<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LineItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineItemController extends Controller
{
    public function index(Category $category): JsonResponse
    {
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lineItems = $category->lineItems()
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($lineItems);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $category = Category::findOrFail($validated['category_id']);

            if ($category->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            DB::beginTransaction();

            $lineItem = LineItem::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            // Update category's actual amount
            $category->actual_amount = $category->lineItems()->sum('amount');
            $category->save();

            DB::commit();

            return response()->json([
                'message' => 'Line item created successfully',
                'line_item' => $lineItem,
                'category' => $category->fresh()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create line item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add update and destroy methods similarly...
}
