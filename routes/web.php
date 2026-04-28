<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pos');
})->middleware(['auth', 'verified'])->name('pos');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/sales', 'sales')->name('sales');
    Route::view('/employees', 'employees')->name('employees')->middleware('can:owner');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory')->middleware('can:owner-admin');
    Route::post('/inventory', [InventoryController::class, 'create'])->name('inventory.create')->middleware('can:owner-admin');
    Route::put('/inventory', [InventoryController::class, 'update'])->name('inventory.update')->middleware('can:owner-admin');
    Route::delete('/inventory', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('can:owner-admin');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
