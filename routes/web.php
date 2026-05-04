<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PosController::class, 'index'])->middleware(['auth'])->name('pos');

Route::middleware(['auth',])->group(function () {
    // pos
    Route::post('/pos/add-item', [PosController::class, 'toggleItem'])->name('pos.toggle.item');
    Route::get('/pos/confirm-checkout', [PosController::class, 'confirmCheckoutView'])->name('pos.confirm.checkout');

    // sales
    Route::view('/sales', 'sales')->name('sales');

    // employees
    Route::view('/employees', 'employees')->name('employees')->middleware('can:owner');

    // inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory')->middleware('can:owner-admin');
    Route::post('/inventory', [InventoryController::class, 'create'])->name('inventory.create')->middleware('can:owner-admin');
    Route::put('/inventory', [InventoryController::class, 'update'])->name('inventory.update')->middleware('can:owner-admin');
    Route::delete('/inventory', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('can:owner-admin');

    // employees + users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
