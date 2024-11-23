<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'budget_month' => 'required|date_format:Y-m-d|unique:budget_summaries,budget_month,NULL,id,user_id,' . auth()->id(),
            'expected_income' => 'required|numeric|min:0',
            'actual_income' => 'nullable|numeric|min:0',
        ];
    }
}
