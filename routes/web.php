<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\QRController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\SetupController;
use App\Http\Controllers\Admin\AttendanceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'branch'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Setup wizard (only when no branches)
    Route::middleware('role:admin')->group(function () {
        Route::get('/setup/branch', [SetupController::class, 'createBranch'])->name('setup.branch.create');
        Route::post('/setup/branch', [SetupController::class, 'storeBranch'])->name('setup.branch.store');
    });

    // Branches
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('branches', BranchController::class)->only(['index','create','store','edit','update','destroy']);
        Route::post('/branches/switch', [BranchController::class, 'switch'])->name('branches.switch');
    });

    // POS + Orders (cashier + manager + admin)
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/pos', [OrderController::class, 'pos'])->name('orders.pos');
        Route::post('/pos', [OrderController::class, 'storeOrder'])->name('orders.store');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/orders/{order}/print', [OrderController::class, 'printReceipt'])->name('orders.print');
    });

    // Master data (admin/manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('tables', TableController::class);

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{product}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');

        // Suppliers
        Route::resource('suppliers', SupplierController::class);

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Reports
        Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');

        // QR Ordering (admin/manager)
        Route::get('/qr', [QRController::class, 'index'])->name('qr.index');
    });

    // Attendance
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
    });
});
