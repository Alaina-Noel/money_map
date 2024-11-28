<?php

namespace App\Http\Controllers;

use App\Models\Paycheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaycheckController extends Controller
{
    public function index(string $month): JsonResponse
    {
        $paychecks = Paycheck::where('user_id', auth()->id())
            ->whereYear('pay_date', substr($month, 0, 4))
            ->whereMonth('pay_date', substr($month, 5, 2))
            ->orderBy('pay_date')
            ->get();

        return response()->json($paychecks);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pay_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'source' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $paycheck = Paycheck::create([
                'user_id' => auth()->id(),
                'pay_date' => $validated['pay_date'],
                'amount' => $validated['amount'],
                'source' => $validated['source'],
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Paycheck created successfully',
                'paycheck' => $paycheck
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create paycheck',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add update and destroy methods similarly...
}
