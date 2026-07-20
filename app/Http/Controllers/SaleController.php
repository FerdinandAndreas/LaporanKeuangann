<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::where('user_id', Auth::id())->with('product');

        // Search & Filter
        if ($search = $request->input('search')) {
            $query->where('item_name', 'like', "%{$search}%");
        }
        if ($buyer = $request->input('buyer')) {
            $query->where('buyer', 'like', "%{$buyer}%");
        }
        if ($startDate = $request->input('start_date')) {
            $query->where('sale_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('sale_date', '<=', $endDate);
        }

        $totalSales = (clone $query)->sum('total_price');

        $sales = $query
            ->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', compact('sales', 'totalSales'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        return view('sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        
        // Sync item_name and unit from product if selected
        if (!empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
            if ($product) {
                $data['item_name'] = $product->name;
                $data['unit'] = $product->unit;
            }
        }
        
        $data['total_price'] = $data['quantity'] * $data['price_per_unit'];

        Sale::create($data);

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil dicatat.');
    }

    public function edit(Sale $sale): View
    {
        $this->authorizeOwner($sale);
        $products = Product::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->authorizeOwner($sale);
        $data = $request->validated();
        
        // Sync item_name and unit from product if selected
        if (!empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
            if ($product) {
                $data['item_name'] = $product->name;
                $data['unit'] = $product->unit;
            }
        }
        
        $data['total_price'] = $data['quantity'] * $data['price_per_unit'];

        $sale->update($data);

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil diperbarui.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorizeOwner($sale);
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil dihapus.');
    }

    public function receipt(Sale $sale): View
    {
        $this->authorizeOwner($sale);
        return view('sales.receipt', compact('sale'));
    }

    public function batchReceipt(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('sales.index')->with('error', 'Silakan pilih minimal satu transaksi penjualan.');
        }

        $sales = Sale::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->orderBy('sale_date', 'asc')
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Ambil nama pembeli pertama untuk header nota
        $buyer = $sales->first()->buyer ?: 'Umum';

        $totalPrice = $sales->sum('total_price');

        return view('sales.batch-receipt', compact('sales', 'buyer', 'totalPrice'));
    }

    private function authorizeOwner(Sale $sale): void
    {
        if ($sale->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
    }
}
