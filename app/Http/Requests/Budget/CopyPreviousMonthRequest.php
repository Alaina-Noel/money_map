<?php

namespace App\Http\Requests\Budget;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CopyPreviousMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $targetMonth = Carbon::parse($this->route('month'));
            $currentMonth = Carbon::now();

            // Check if target month is current month
            if ($targetMonth->format('Y-m') === $currentMonth->format('Y-m')) {
                $validator->errors()->add(
                    'month',
                    'Cannot copy categories to current month. Please select a future month.'
                );
            }

            // Check if target month already has categories
            $hasExistingCategories = Category::where('user_id', $this->user()->id)
                ->whereYear('budget_month', $targetMonth->year)
                ->whereMonth('budget_month', $targetMonth->month)
                ->exists();

            if ($hasExistingCategories) {
                $validator->errors()->add(
                    'month',
                    'Categories already exist for this month. Cannot copy categories twice.'
                );
            }

            // Check if previous month has any categories to copy
            $previousMonth = $targetMonth->copy()->subMonth();
            $hasPreviousCategories = Category::where('user_id', $this->user()->id)
                ->whereYear('budget_month', $previousMonth->year)
                ->whereMonth('budget_month', $previousMonth->month)
                ->exists();

            if (!$hasPreviousCategories) {
                $validator->errors()->add(
                    'month',
                    'No categories found in previous month to copy from.'
                );
            }
        });
    }
}
