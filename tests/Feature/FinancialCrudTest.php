<?php

namespace Tests\Feature;

use App\Models\Capital;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
    }

    // ===========================
    // DASHBOARD
    // ===========================

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->owner)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHasAll([
            'totalCapital', 'totalPurchases', 'totalSales',
            'profit', 'runningCapital', 'recentTransactions',
            'chartMonths', 'chartPurchases', 'chartSales',
        ]);
    }

    public function test_dashboard_computes_correct_financial_metrics(): void
    {
        Capital::factory()->create(['user_id' => $this->owner->id, 'amount' => 5000000, 'date' => now()]);
        Purchase::factory()->create(['user_id' => $this->owner->id, 'total_price' => 1000000, 'purchase_date' => now()]);
        Sale::factory()->create(['user_id' => $this->owner->id, 'total_price' => 1500000, 'sale_date' => now()]);

        $response = $this->actingAs($this->owner)->get('/dashboard');
        $response->assertStatus(200);

        // Running capital = 5000000 + (1500000 - 1000000) = 5500000
        $this->assertEquals(5500000, $response->viewData('runningCapital'));
        $this->assertEquals(500000, $response->viewData('profit'));
    }

    public function test_dashboard_period_filter_works(): void
    {
        // Create a purchase dated 2 months ago (should NOT appear in current month filter)
        Purchase::factory()->create([
            'user_id' => $this->owner->id,
            'total_price' => 9999999,
            'purchase_date' => now()->subMonths(2),
        ]);

        $response = $this->actingAs($this->owner)->get('/dashboard?filter=month');
        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('totalPurchases'));
    }

    // ===========================
    // PRODUCTS
    // ===========================

    public function test_product_index_is_accessible(): void
    {
        $response = $this->actingAs($this->owner)->get('/products');
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    public function test_can_create_product(): void
    {
        $response = $this->actingAs($this->owner)->post('/products', [
            'name' => 'Beras Pandan Wangi',
            'unit' => 'kg',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Beras Pandan Wangi', 'unit' => 'kg']);
    }

    public function test_product_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->owner)->post('/products', []);
        $response->assertSessionHasErrors(['name', 'unit']);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->owner)->patch("/products/{$product->id}", [
            'name' => 'Beras IR64 Premium',
            'unit' => 'karung',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Beras IR64 Premium']);
    }

    public function test_can_delete_product_without_transactions(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->owner)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_cannot_delete_product_with_existing_purchases(): void
    {
        $product = Product::factory()->create();
        Purchase::factory()->create(['user_id' => $this->owner->id, 'product_id' => $product->id]);

        $response = $this->actingAs($this->owner)->delete("/products/{$product->id}");
        $response->assertRedirect('/products');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    // ===========================
    // CAPITALS
    // ===========================

    public function test_capital_index_is_accessible(): void
    {
        $response = $this->actingAs($this->owner)->get('/capitals');
        $response->assertStatus(200);
        $response->assertViewIs('capitals.index');
        $response->assertViewHasAll(['capitals', 'totalCapital']);
    }

    public function test_can_create_capital(): void
    {
        $response = $this->actingAs($this->owner)->post('/capitals', [
            'amount' => 10000000,
            'type' => 'awal',
            'description' => 'Modal awal toko beras',
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/capitals');
        $this->assertDatabaseHas('capitals', [
            'user_id' => $this->owner->id,
            'amount' => 10000000,
            'type' => 'awal',
        ]);
    }

    public function test_capital_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->owner)->post('/capitals', []);
        $response->assertSessionHasErrors(['amount', 'type', 'date']);
    }

    public function test_capital_store_rejects_invalid_type(): void
    {
        $response = $this->actingAs($this->owner)->post('/capitals', [
            'amount' => 5000000,
            'type' => 'invalid_type',
            'date' => now()->toDateString(),
        ]);
        $response->assertSessionHasErrors(['type']);
    }

    public function test_can_update_capital(): void
    {
        $capital = Capital::factory()->create(['user_id' => $this->owner->id]);

        $response = $this->actingAs($this->owner)->patch("/capitals/{$capital->id}", [
            'amount' => 20000000,
            'type' => 'tambahan',
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/capitals');
        $this->assertDatabaseHas('capitals', ['id' => $capital->id, 'amount' => 20000000]);
    }

    public function test_cannot_update_other_users_capital(): void
    {
        $otherUser = User::factory()->create();
        $capital = Capital::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->owner)->patch("/capitals/{$capital->id}", [
            'amount' => 1,
            'type' => 'awal',
            'date' => now()->toDateString(),
        ]);
        $response->assertStatus(403);
    }

    public function test_can_delete_capital(): void
    {
        $capital = Capital::factory()->create(['user_id' => $this->owner->id]);

        $response = $this->actingAs($this->owner)->delete("/capitals/{$capital->id}");

        $response->assertRedirect('/capitals');
        $this->assertDatabaseMissing('capitals', ['id' => $capital->id]);
    }

    // ===========================
    // PURCHASES
    // ===========================

    public function test_purchase_index_is_accessible(): void
    {
        $response = $this->actingAs($this->owner)->get('/purchases');
        $response->assertStatus(200);
        $response->assertViewIs('purchases.index');
        $response->assertViewHasAll(['purchases', 'totalPurchases']);
    }

    public function test_can_create_purchase_with_manual_item_name(): void
    {
        $response = $this->actingAs($this->owner)->post('/purchases', [
            'item_name' => 'Beras Ramos',
            'quantity' => 100,
            'unit' => 'kg',
            'price_per_unit' => 12000,
            'purchase_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/purchases');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->owner->id,
            'item_name' => 'Beras Ramos',
            'total_price' => 1200000,
        ]);
    }

    public function test_can_create_purchase_with_product_reference(): void
    {
        $product = Product::factory()->create(['name' => 'Beras Premium', 'unit' => 'kg']);

        $response = $this->actingAs($this->owner)->post('/purchases', [
            'product_id' => $product->id,
            'quantity' => 50,
            'unit' => 'kg',
            'price_per_unit' => 15000,
            'purchase_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/purchases');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->owner->id,
            'product_id' => $product->id,
            'total_price' => 750000,
        ]);
    }

    public function test_purchase_total_is_auto_calculated(): void
    {
        $this->actingAs($this->owner)->post('/purchases', [
            'item_name' => 'Beras Test',
            'quantity' => 10,
            'unit' => 'kg',
            'price_per_unit' => 13000,
            'purchase_date' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('purchases', ['total_price' => 130000]);
    }

    public function test_cannot_delete_other_users_purchase(): void
    {
        $otherUser = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->owner)->delete("/purchases/{$purchase->id}");
        $response->assertStatus(403);
    }

    // ===========================
    // SALES
    // ===========================

    public function test_sale_index_is_accessible(): void
    {
        $response = $this->actingAs($this->owner)->get('/sales');
        $response->assertStatus(200);
        $response->assertViewIs('sales.index');
        $response->assertViewHasAll(['sales', 'totalSales']);
    }

    public function test_can_create_sale(): void
    {
        $response = $this->actingAs($this->owner)->post('/sales', [
            'item_name' => 'Beras Setra Ramos',
            'quantity' => 50,
            'unit' => 'kg',
            'price_per_unit' => 14000,
            'sale_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/sales');
        $this->assertDatabaseHas('sales', [
            'user_id' => $this->owner->id,
            'item_name' => 'Beras Setra Ramos',
            'total_price' => 700000,
        ]);
    }

    public function test_sale_total_is_auto_calculated(): void
    {
        $this->actingAs($this->owner)->post('/sales', [
            'item_name' => 'Beras Test',
            'quantity' => 5,
            'unit' => 'kg',
            'price_per_unit' => 16000,
            'sale_date' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('sales', ['total_price' => 80000]);
    }

    public function test_cannot_delete_other_users_sale(): void
    {
        $otherUser = User::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->owner)->delete("/sales/{$sale->id}");
        $response->assertStatus(403);
    }

    public function test_can_delete_own_sale(): void
    {
        $sale = Sale::factory()->create(['user_id' => $this->owner->id]);

        $response = $this->actingAs($this->owner)->delete("/sales/{$sale->id}");

        $response->assertRedirect('/sales');
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }

    public function test_purchase_and_sale_observer_manipulates_stock(): void
    {
        $product = Product::factory()->create(['current_stock' => 0]);

        // Purchase increases stock
        $purchase = Purchase::factory()->create([
            'user_id' => $this->owner->id,
            'product_id' => $product->id,
            'quantity' => 100,
        ]);
        $this->assertEquals(100, $product->fresh()->current_stock);

        // Sale decreases stock
        $sale = Sale::factory()->create([
            'user_id' => $this->owner->id,
            'product_id' => $product->id,
            'quantity' => 40,
        ]);
        $this->assertEquals(60, $product->fresh()->current_stock);

        // Update purchase adjusts stock
        $purchase->update(['quantity' => 120]);
        $this->assertEquals(80, $product->fresh()->current_stock); // 0 + 120 - 40 = 80

        // Delete purchase decreases stock
        $purchase->delete();
        $this->assertEquals(-40, $product->fresh()->current_stock); // 0 - 40 = -40
    }

    public function test_cannot_create_sale_if_stock_insufficient(): void
    {
        $product = Product::factory()->create(['current_stock' => 10]);

        $response = $this->actingAs($this->owner)->post('/sales', [
            'product_id' => $product->id,
            'quantity' => 15, // more than 10
            'price_per_unit' => 10000,
            'sale_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['quantity']);
    }

    public function test_report_csv_export_returns_stream_download(): void
    {
        $response = $this->actingAs($this->owner)->get('/reports/csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Laporan_Keuangan_Semua_sd_Kini.csv');
    }

    public function test_report_print_returns_successful_response(): void
    {
        $response = $this->actingAs($this->owner)->get('/reports/print');
        $response->assertStatus(200);
        $response->assertViewIs('reports.print');
    }
}
