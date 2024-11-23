<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BudgetSummary;
use Symfony\Component\HttpFoundation\Response;

class CheckBudgetOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $budgetSummary = $request->route('budgetSummary');

        if ($budgetSummary && $budgetSummary->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
