<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'expected' => 'required|numeric|min:0',
            'actual' => 'nullable|numeric|min:0',
            'budget_month' => 'required|date_format:Y-m-d',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')
                    ->where(function ($query) {
                        return $query->where('user_id', $this->user_id)
                            ->where('budget_month', $this->budget_month);
                    })
                    ->ignore($this->category),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A category with this name already exists for this month.',
            'expected.min' => 'The expected amount must be greater than zero.',
        ];
    }
}
