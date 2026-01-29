<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OperasionalController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Warung Libur (harus sebelum resource route)
    Route::get('/warung/libur', [WarungController::class, 'libur'])->name('warung.libur');
    Route::post('/warung/libur', [WarungController::class, 'storeLibur'])->name('warung.libur.store');
    Route::delete('/warung/libur/{libur}', [WarungController::class, 'destroyLibur'])->name('warung.libur.destroy');
    Route::get('/warung/laporan-libur', [WarungController::class, 'laporanLibur'])->name('warung.laporan-libur');
    Route::get('/warung/laporan-libur/pdf', [WarungController::class, 'exportLaporanLiburPdf'])->name('warung.laporan-libur.pdf');
    
    // Warung Resource
    Route::resource('warung', WarungController::class)->except(['show']);
    
    // Transaksi
    Route::get('/transaksi/export-pdf', [TransaksiController::class, 'exportPdf'])->name('transaksi.export-pdf');
    Route::resource('transaksi', TransaksiController::class);
    
    // Operasional
    Route::resource('operasional', OperasionalController::class)->except(['show']);
    
    // Stok History (harus sebelum stok routes dengan parameter)
    Route::get('/stok/history', [StokController::class, 'history'])->name('stok.history');
    Route::get('/stok/history/pdf', [StokController::class, 'exportHistoryPdf'])->name('stok.history.pdf');
    
    // Stock Besar (Items + Stock + Opname)
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/create', [StokController::class, 'create'])->name('stok.create');
    Route::post('/stok', [StokController::class, 'store'])->name('stok.store');
    Route::get('/stok/opname', [StokController::class, 'opname'])->name('stok.opname');
    Route::post('/stok/opname', [StokController::class, 'storeOpname'])->name('stok.opname.store');
    Route::get('/stok/{stok}/restok', [StokController::class, 'restok'])->name('stok.restok');
    Route::post('/stok/{stok}/restok', [StokController::class, 'storeRestok'])->name('stok.restok.store');
    Route::get('/stok/{stok}/edit', [StokController::class, 'edit'])->name('stok.edit');
    Route::put('/stok/{stok}', [StokController::class, 'update'])->name('stok.update');
    Route::delete('/stok/{stok}', [StokController::class, 'destroy'])->name('stok.destroy');
    
    // Laporan
    Route::get('/laporan/konsolidasi', [LaporanController::class, 'konsolidasi'])->name('laporan.konsolidasi');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
});
