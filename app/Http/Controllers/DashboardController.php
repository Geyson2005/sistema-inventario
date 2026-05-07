<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockExit;
use App\Models\StockMovement;
use App\Models\StockEntryBatch;
use App\Models\StockExitBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalProducts = Product::active()->count();
        $lowStockProducts = Product::lowStock()->count();
        $outOfStockProducts = Product::outOfStock()->count();
        
        // Movimientos del mes actual
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $entriesThisMonth = StockEntry::whereBetween('entry_date', [$startOfMonth, $endOfMonth])->sum('quantity');
        $exitsThisMonth = StockExit::whereBetween('exit_date', [$startOfMonth, $endOfMonth])->sum('quantity');
        
        // Lotes del mes
        $entryBatchesThisMonth = StockEntryBatch::whereBetween('entry_date', [$startOfMonth, $endOfMonth])->count();
        $exitBatchesThisMonth = StockExitBatch::whereBetween('exit_date', [$startOfMonth, $endOfMonth])->count();
        
        // Productos con stock bajo o agotado
        $alertProducts = Product::where(function($query) {
                $query->whereColumn('current_stock', '<=', 'min_stock');
            })
            ->with(['category', 'unit'])
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();
        
        // Últimos movimientos
        $recentMovements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Últimos lotes de entrada
        $recentEntryBatches = StockEntryBatch::with(['supplier', 'user'])
            ->orderBy('entry_date', 'desc')
            ->limit(5)
            ->get();

        // Últimos lotes de salida
        $recentExitBatches = StockExitBatch::with(['user'])
            ->orderBy('exit_date', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'entriesThisMonth',
            'exitsThisMonth',
            'entryBatchesThisMonth',
            'exitBatchesThisMonth',
            'alertProducts',
            'recentMovements',
            'recentEntryBatches',
            'recentExitBatches'
        ));
    }
}