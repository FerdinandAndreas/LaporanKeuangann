<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver
{
    /**
     * Saat pembelian baru dibuat → tambah stok produk.
     */
    public function created(Purchase $purchase): void
    {
        if ($purchase->product_id) {
            $purchase->product()->increment('current_stock', $purchase->quantity);
        }
    }

    /**
     * Saat pembelian diubah → hitung selisih qty dan adjust stok.
     */
    public function updated(Purchase $purchase): void
    {
        // Cek apakah qty atau product_id berubah
        $oldQty = $purchase->getOriginal('quantity') ?? 0;
        $newQty = $purchase->quantity;
        $oldProductId = $purchase->getOriginal('product_id');
        $newProductId = $purchase->product_id;

        // Jika produk berubah: kembalikan stok produk lama, tambah ke produk baru
        if ($oldProductId !== $newProductId) {
            if ($oldProductId) {
                $purchase->product()->getQuery()->where('id', $oldProductId)
                    ->decrement('current_stock', $oldQty);
            }
            if ($newProductId) {
                $purchase->product()->increment('current_stock', $newQty);
            }
        } elseif ($oldProductId && $oldQty != $newQty) {
            // Produk sama, qty berubah
            $diff = $newQty - $oldQty;
            if ($diff > 0) {
                $purchase->product()->increment('current_stock', $diff);
            } else {
                $purchase->product()->decrement('current_stock', abs($diff));
            }
        }
    }

    /**
     * Saat pembelian dihapus → kurangi stok produk.
     */
    public function deleted(Purchase $purchase): void
    {
        if ($purchase->product_id) {
            $purchase->product()->decrement('current_stock', $purchase->quantity);
        }
    }
}
