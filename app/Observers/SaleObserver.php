<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    /**
     * Saat penjualan baru dibuat → kurangi stok produk.
     */
    public function created(Sale $sale): void
    {
        if ($sale->product_id) {
            $sale->product()->decrement('current_stock', $sale->quantity);
        }
    }

    /**
     * Saat penjualan diubah → hitung selisih qty dan adjust stok.
     */
    public function updated(Sale $sale): void
    {
        $oldQty = $sale->getOriginal('quantity') ?? 0;
        $newQty = $sale->quantity;
        $oldProductId = $sale->getOriginal('product_id');
        $newProductId = $sale->product_id;

        // Jika produk berubah: kembalikan stok produk lama, kurangi dari produk baru
        if ($oldProductId !== $newProductId) {
            if ($oldProductId) {
                $sale->product()->getQuery()->where('id', $oldProductId)
                    ->increment('current_stock', $oldQty);
            }
            if ($newProductId) {
                $sale->product()->decrement('current_stock', $newQty);
            }
        } elseif ($oldProductId && $oldQty != $newQty) {
            // Produk sama, qty berubah
            $diff = $newQty - $oldQty;
            if ($diff > 0) {
                $sale->product()->decrement('current_stock', $diff);
            } else {
                $sale->product()->increment('current_stock', abs($diff));
            }
        }
    }

    /**
     * Saat penjualan dihapus → kembalikan stok produk.
     */
    public function deleted(Sale $sale): void
    {
        if ($sale->product_id) {
            $sale->product()->increment('current_stock', $sale->quantity);
        }
    }
}
