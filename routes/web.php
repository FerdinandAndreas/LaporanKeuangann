<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CapitalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('capitals', CapitalController::class)->except(['show']);
    Route::resource('purchases', PurchaseController::class)->except(['show']);
    Route::get('/sales/batch-receipt', [SaleController::class, 'batchReceipt'])->name('sales.batch-receipt');
    Route::resource('sales', SaleController::class)->except(['show']);

    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/print', [ReportController::class, 'printReport'])->name('reports.print');

    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
