<?php
namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;
class BudgetIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Only authorized users can make this request
    }

    public function rules(): array
    {
        return [
            'budget_month' => 'required|date_format:Y-m',
            'expected_income' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'budget_month.required' => 'The budget month is required.',
            'budget_month.date_format' => 'The budget month must be in the format YYYY-MM.',
            'expected_income.required' => 'The expected income is required.',
            'expected_income.numeric' => 'The expected income must be a numeric value.',
            'expected_income.min' => 'The expected income must be at least 0.',
        ];
    }
}
