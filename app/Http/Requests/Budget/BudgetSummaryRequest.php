<?php
namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class BudgetSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'budget_month' => 'required|date_format:Y-m',
        ];
    }
}
