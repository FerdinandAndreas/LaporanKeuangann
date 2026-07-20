<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Export data to CSV (opens seamlessly in Excel).
     */
    public function exportCsv(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filename = "Laporan_Keuangan_" . ($startDate ?: 'Semua') . "_sd_" . ($endDate ?: 'Kini') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $capitalQuery = Capital::where('user_id', $userId);
        $purchaseQuery = Purchase::where('user_id', $userId)->with('product');
        $saleQuery = Sale::where('user_id', $userId)->with('product');

        if ($startDate && $endDate) {
            $capitalQuery->whereBetween('date', [$startDate, $endDate]);
            $purchaseQuery->whereBetween('purchase_date', [$startDate, $endDate]);
            $saleQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $capitals = $capitalQuery->orderBy('date', 'desc')->get();
        $purchases = $purchaseQuery->orderBy('purchase_date', 'desc')->get();
        $sales = $saleQuery->orderBy('sale_date', 'desc')->get();

        $callback = function() use ($capitals, $purchases, $sales) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Section 1: Summary
            fputcsv($file, ['LAPORAN KEUANGAN TOKO BERAS']);
            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN FINANSIAL']);
            fputcsv($file, ['Total Modal Masuk', number_format($capitals->sum('amount'), 2, ',', '.')]);
            fputcsv($file, ['Total Pembelian (HPP)', number_format($purchases->sum('total_price'), 2, ',', '.')]);
            fputcsv($file, ['Total Penjualan (Omzet)', number_format($sales->sum('total_price'), 2, ',', '.')]);
            fputcsv($file, ['Laba / Rugi', number_format($sales->sum('total_price') - $purchases->sum('total_price'), 2, ',', '.')]);
            fputcsv($file, []);

            // Section 2: Capital
            fputcsv($file, ['DATA MODAL']);
            fputcsv($file, ['Tanggal', 'Tipe', 'Jumlah Modal (Rp)', 'Keterangan']);
            foreach ($capitals as $c) {
                fputcsv($file, [$c->date->format('d-m-Y'), $c->type === 'awal' ? 'Modal Awal' : 'Modal Tambahan', $c->amount, $c->description ?: '-']);
            }
            fputcsv($file, []);

            // Section 3: Purchases
            fputcsv($file, ['DATA PEMBELIAN']);
            fputcsv($file, ['Tanggal', 'Nama Barang', 'Jumlah', 'Satuan', 'Harga Satuan (Rp)', 'Total (Rp)', 'Supplier']);
            foreach ($purchases as $p) {
                fputcsv($file, [$p->purchase_date->format('d-m-Y'), $p->item_name, $p->quantity, $p->unit, $p->price_per_unit, $p->total_price, $p->supplier ?: '-']);
            }
            fputcsv($file, []);

            // Section 4: Sales
            fputcsv($file, ['DATA PENJUALAN']);
            fputcsv($file, ['Tanggal', 'Nama Barang', 'Jumlah', 'Satuan', 'Harga Satuan (Rp)', 'Total (Rp)', 'Pembeli']);
            foreach ($sales as $s) {
                fputcsv($file, [$s->sale_date->format('d-m-Y'), $s->item_name, $s->quantity, $s->unit, $s->price_per_unit, $s->total_price, $s->buyer ?: '-']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print report view (saves natively to PDF via browser print).
     */
    public function printReport(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $capitalQuery = Capital::where('user_id', $userId);
        $purchaseQuery = Purchase::where('user_id', $userId)->with('product');
        $saleQuery = Sale::where('user_id', $userId)->with('product');

        if ($startDate && $endDate) {
            $capitalQuery->whereBetween('date', [$startDate, $endDate]);
            $purchaseQuery->whereBetween('purchase_date', [$startDate, $endDate]);
            $saleQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $capitals = $capitalQuery->orderBy('date', 'desc')->get();
        $purchases = $purchaseQuery->orderBy('purchase_date', 'desc')->get();
        $sales = $saleQuery->orderBy('sale_date', 'desc')->get();

        $totalCapital = $capitals->sum('amount');
        $totalPurchases = $purchases->sum('total_price');
        $totalSales = $sales->sum('total_price');
        $profit = $totalSales - $totalPurchases;

        return view('reports.print', compact(
            'capitals', 'purchases', 'sales',
            'totalCapital', 'totalPurchases', 'totalSales', 'profit',
            'startDate', 'endDate'
        ));
    }
}
