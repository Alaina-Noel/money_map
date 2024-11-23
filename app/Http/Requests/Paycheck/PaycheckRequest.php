<?php

namespace App\Http\Requests\Paycheck;

use Illuminate\Foundation\Http\FormRequest;

class PaycheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'pay_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'The paycheck amount must be greater than zero.',
        ];
    }
}
