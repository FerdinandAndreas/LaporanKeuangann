<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'exists:products,id'],
            'item_name' => ['required_without:product_id', 'nullable', 'string', 'max:150'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
            'price_per_unit' => ['required', 'numeric', 'min:0.01'],
            'buyer' => ['nullable', 'string', 'max:100'],
            'sale_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
