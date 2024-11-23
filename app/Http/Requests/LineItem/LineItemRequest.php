<?php

namespace App\Http\Requests\LineItem;

use Illuminate\Foundation\Http\FormRequest;

class LineItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'budget_month' => 'required|date_format:Y-m-d',
        ];
    }
}
