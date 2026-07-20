<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->input('filter', 'all'); // options: today, week, month, all, custom
        $startDate = null;
        $endDate = null;

        if ($filter === 'today') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
        } elseif ($filter === 'week') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($filter === 'month') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($filter === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        // Aggregate query calculations
        $capitalQuery = Capital::where('user_id', $userId);
        $purchaseQuery = Purchase::where('user_id', $userId);
        $saleQuery = Sale::where('user_id', $userId);

        if ($startDate && $endDate) {
            $capitalQuery->whereBetween('date', [$startDate, $endDate]);
            $purchaseQuery->whereBetween('purchase_date', [$startDate, $endDate]);
            $saleQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $totalCapital = (float) $capitalQuery->sum('amount');
        $totalPurchases = (float) $purchaseQuery->sum('total_price');
        $totalSales = (float) $saleQuery->sum('total_price');

        $profit = $totalSales - $totalPurchases;
        
        // Modal berjalan = total modal sepanjang waktu + akumulasi laba/rugi sepanjang waktu
        $allTimeCapital = (float) Capital::where('user_id', $userId)->sum('amount');
        $allTimePurchases = (float) Purchase::where('user_id', $userId)->sum('total_price');
        $allTimeSales = (float) Sale::where('user_id', $userId)->sum('total_price');
        $runningCapital = $allTimeCapital + ($allTimeSales - $allTimePurchases);

        // Fetch recent activities
        $recentPurchases = Purchase::where('user_id', $userId)
            ->with('product')
            ->orderBy('purchase_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'pembelian';
                $item->activity_date = $item->purchase_date->toDateString();
                return $item;
            });

        $recentSales = Sale::where('user_id', $userId)
            ->with('product')
            ->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'penjualan';
                $item->activity_date = $item->sale_date->toDateString();
                return $item;
            });

        $recentTransactions = $recentPurchases->concat($recentSales)
            ->sortByDesc('created_at')
            ->sortByDesc('activity_date')
            ->take(5);

        // Prepare chart data for the last 6 months
        $months = [];
        $monthlyPurchases = [];
        $monthlySales = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $months[] = $monthDate->translatedFormat('F Y');

            $monthlyPurchases[] = (float) Purchase::where('user_id', $userId)
                ->whereYear('purchase_date', $monthDate->year)
                ->whereMonth('purchase_date', $monthDate->month)
                ->sum('total_price');

            $monthlySales[] = (float) Sale::where('user_id', $userId)
                ->whereYear('sale_date', $monthDate->year)
                ->whereMonth('sale_date', $monthDate->month)
                ->sum('total_price');
        }

        $chartMonths = json_encode($months);
        $chartPurchases = json_encode($monthlyPurchases);
        $chartSales = json_encode($monthlySales);

        return view('dashboard', compact(
            'totalCapital',
            'totalPurchases',
            'totalSales',
            'profit',
            'runningCapital',
            'recentTransactions',
            'filter',
            'startDate',
            'endDate',
            'chartMonths',
            'chartPurchases',
            'chartSales'
        ));
    }
}
