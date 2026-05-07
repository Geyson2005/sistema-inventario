<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController; 
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController; 
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;
use App\Http\Controllers\StockEntryBatchController;
use App\Http\Controllers\StockExitBatchController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Ruta Principal - Redirige según autenticación
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Productos (Acceso para todos los usuarios autenticados)
    Route::resource('products', ProductController::class);
    
    // Búsqueda rápida de productos (para autocomplete)
    Route::get('/products-search', [ProductController::class, 'search'])->name('products.search');

    Route::resource('stock-entry-batches', StockEntryBatchController::class)->names([
        'index' => 'stock-entry-batches.index',
        'create' => 'stock-entry-batches.create',
        'store' => 'stock-entry-batches.store',
        'show' => 'stock-entry-batches.show',
        'destroy' => 'stock-entry-batches.destroy',
    ]);
    
    // ⭐ SALIDAS DE STOCK (MÚLTIPLES) - NUEVO
    Route::resource('stock-exit-batches', StockExitBatchController::class)->names([
        'index' => 'stock-exit-batches.index',
        'create' => 'stock-exit-batches.create',
        'store' => 'stock-exit-batches.store',
        'show' => 'stock-exit-batches.show',
        'destroy' => 'stock-exit-batches.destroy',
    ]);
    
    // Entradas de Stock
    Route::resource('stock-entries', StockEntryController::class);
    
    // Salidas de Stock
    Route::resource('stock-exits', StockExitController::class);
    
    // Historial de Movimientos
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('stock-movements/{product}', [StockMovementController::class, 'show'])->name('stock-movements.show');
    
    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas Solo para Administradores
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    
    // Categorías
    Route::resource('categories', CategoryController::class);
    
    // Unidades de Medida
    Route::resource('units', UnitController::class);

    // Proveedores
    Route::resource('suppliers', SupplierController::class);

    // Ajustes de Stock
    Route::resource('stock-adjustments', StockAdjustmentController::class);
    
    // Gestión de Usuarios
    Route::resource('users', UserController::class);

    // Clientes (Solo Administradores)
    Route::resource('clients', ClientController::class);
    Route::get('/clients-search', [ClientController::class, 'search'])->name('clients.search');
    
    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

require __DIR__.'/auth.php';