<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     * RF12, RF19, RF20: Visualizar historial con filtros
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product.category', 'product.unit', 'user']);

        // Filtro por búsqueda (producto)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtro por producto
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filtro por tipo de movimiento
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Ordenar por fecha más reciente
        $movements = $query->orderBy('created_at', 'desc')->paginate(20);

        // Para los filtros
        $products = Product::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products', 'categories'));
    }

    /**
     * Display the specified resource.
     * Historial de un producto específico
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit']);

        // Obtener todos los movimientos del producto
        $movements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        // Estadísticas del producto
        $totalEntries = $product->stockMovements()->where('movement_type', 'entry')->sum('quantity');
        $totalExits = $product->stockMovements()->where('movement_type', 'exit')->sum('quantity');
        $totalAdjustments = $product->stockMovements()->where('movement_type', 'adjustment')->count();

        return view('stock-movements.show', compact('product', 'movements', 'totalEntries', 'totalExits', 'totalAdjustments'));
    }
}