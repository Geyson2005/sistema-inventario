<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * RF16: Listar todos los productos con filtros
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit']);

        // Filtro por búsqueda (código o nombre)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por estado de stock
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('current_stock', '<=', 'min_stock')
                          ->where('current_stock', '>', 0);
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereColumn('current_stock', '>', 'min_stock');
                    break;
            }
        }

        // Ordenar por nombre por defecto
        $products = $query->orderBy('name', 'asc')->paginate(15);

        // Para los filtros
        $categories = Category::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     * RF13: Crear nuevos productos
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     * RF13: Guardar nuevo producto
     */
    public function store(Request $request)
    {
        // Validaciones
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado',
            'name.required' => 'El nombre es obligatorio',
            'category_id.required' => 'Debe seleccionar una categoría',
            'unit_id.required' => 'Debe seleccionar una unidad de medida',
            'min_stock.required' => 'El stock mínimo es obligatorio',
            'min_stock.min' => 'El stock mínimo no puede ser negativo',
        ]);

        // Crear el producto
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     * RF09: Consultar stock actual por producto
     * RF12: Visualizar historial de movimientos
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'stockMovements.user']);
        
        // Últimos 20 movimientos del producto
        $movements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('products.show', compact('product', 'movements'));
    }

    /**
     * Show the form for editing the specified resource.
     * RF14: Editar datos de productos existentes
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     * RF14: Actualizar producto
     */
    public function update(Request $request, Product $product)
    {
        // Validaciones
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado',
            'name.required' => 'El nombre es obligatorio',
            'category_id.required' => 'Debe seleccionar una categoría',
            'unit_id.required' => 'Debe seleccionar una unidad de medida',
            'min_stock.required' => 'El stock mínimo es obligatorio',
            'min_stock.min' => 'El stock mínimo no puede ser negativo',
        ]);

        // Actualizar el producto
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * RF15: Eliminar productos (solo si no tienen movimientos)
     */
    public function destroy(Product $product)
    {
        // Verificar si tiene movimientos
        if (!$product->canDelete()) {
            return redirect()->route('products.index')
                ->with('error', 'No se puede eliminar este producto porque tiene movimientos registrados.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Search products (AJAX)
     * Para autocompletado en formularios
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $products = Product::active()
            ->where(function($query) use ($term) {
                $query->where('code', 'like', "%{$term}%")
                      ->orWhere('name', 'like', "%{$term}%");
            })
            ->with(['category', 'unit'])
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}