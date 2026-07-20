<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Capital;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_models_and_relationships(): void
    {
        // 1. Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'owner',
        ]);

        // 2. Create a product
        $product = Product::create([
            'name' => 'Beras IR64',
            'unit' => 'kg',
        ]);

        // 3. Create a capital entry
        $capital = Capital::create([
            'user_id' => $user->id,
            'amount' => 5000000.50,
            'type' => 'awal',
            'description' => 'Modal Awal Toko',
            'date' => now()->toDateString(),
        ]);

        // Verify Capital relationship and casting
        $this->assertEquals($user->id, $capital->user->id);
        $this->assertEquals('5000000.50', $capital->amount); // verify decimal cast
        $this->assertTrue($user->capitals->contains($capital));

        // 4. Create a purchase entry
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'item_name' => 'Beras IR64',
            'quantity' => 100.50,
            'unit' => 'kg',
            'price_per_unit' => 12000.00,
            'total_price' => 1206000.00,
            'supplier' => 'Supplier A',
            'purchase_date' => now()->toDateString(),
            'notes' => 'Pembelian stok awal',
        ]);

        // Verify Purchase relationship and casting
        $this->assertEquals($user->id, $purchase->user->id);
        $this->assertEquals($product->id, $purchase->product->id);
        $this->assertEquals('100.50', $purchase->quantity);
        $this->assertEquals('1206000.00', $purchase->total_price);
        $this->assertTrue($user->purchases->contains($purchase));
        $this->assertTrue($product->purchases->contains($purchase));

        // 5. Create a sale entry
        $sale = Sale::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'item_name' => 'Beras IR64',
            'quantity' => 10.00,
            'unit' => 'kg',
            'price_per_unit' => 15000.00,
            'total_price' => 150000.00,
            'buyer' => 'Pelanggan Umum',
            'sale_date' => now()->toDateString(),
            'notes' => 'Penjualan eceran',
        ]);

        // Verify Sale relationship and casting
        $this->assertEquals($user->id, $sale->user->id);
        $this->assertEquals($product->id, $sale->product->id);
        $this->assertEquals('10.00', $sale->quantity);
        $this->assertEquals('150000.00', $sale->total_price);
        $this->assertTrue($user->sales->contains($sale));
        $this->assertTrue($product->sales->contains($sale));
    }
}
