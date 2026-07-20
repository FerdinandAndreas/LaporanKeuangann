<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
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

    /**
     * Validasi tambahan setelah rule utama lulus:
     * Cek stok produk mencukupi jika product_id dipilih.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $productId = $this->input('product_id');
                $quantity = (float) $this->input('quantity', 0);

                if ($productId && $quantity > 0) {
                    $product = Product::find($productId);
                    if ($product && (float) $product->current_stock < $quantity) {
                        $validator->errors()->add(
                            'quantity',
                            "Stok {$product->name} tidak mencukupi. Stok tersedia: " .
                            number_format((float) $product->current_stock, 2, ',', '.') . " {$product->unit}."
                        );
                    }
                }
            },
        ];
    }
}
