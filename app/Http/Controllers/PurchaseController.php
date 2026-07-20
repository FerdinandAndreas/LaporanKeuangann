<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Purchase::where('user_id', Auth::id())->with('product');

        // Search & Filter
        if ($search = $request->input('search')) {
            $query->where('item_name', 'like', "%{$search}%");
        }
        if ($supplier = $request->input('supplier')) {
            $query->where('supplier', 'like', "%{$supplier}%");
        }
        if ($startDate = $request->input('start_date')) {
            $query->where('purchase_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('purchase_date', '<=', $endDate);
        }

        $totalPurchases = (clone $query)->sum('total_price');

        $purchases = $query
            ->orderBy('purchase_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'totalPurchases'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        return view('purchases.create', compact('products'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
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

        Purchase::create($data);

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian berhasil dicatat.');
    }

    public function edit(Purchase $purchase): View
    {
        $this->authorizeOwner($purchase);
        $products = Product::orderBy('name')->get();
        return view('purchases.edit', compact('purchase', 'products'));
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $this->authorizeOwner($purchase);
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

        $purchase->update($data);

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian berhasil diperbarui.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $this->authorizeOwner($purchase);
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian berhasil dihapus.');
    }

    private function authorizeOwner(Purchase $purchase): void
    {
        if ($purchase->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
    }
}
