<?php

namespace App\Http\Controllers;

use App\Models\StockEntry;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     * RF01: Listar todas las entradas
     */
    public function index(Request $request)
    {
        $query = StockEntry::with(['product.unit', 'supplier', 'user']);

        // Filtro por búsqueda (producto o proveedor)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('supplier', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filtro por producto
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtro por proveedor
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filtro por tipo de documento
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->where('entry_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('entry_date', '<=', $request->end_date);
        }

        // Ordenar por fecha más reciente
        $entries = $query->orderBy('entry_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        // Para los filtros
        $products = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('stock-entries.index', compact('entries', 'products', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     * RF01: Formulario para registrar entrada
     */
    public function create(Request $request)
    {
        $products = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        
        // Si viene un product_id por URL (desde detalles del producto)
        $selectedProductId = $request->get('product_id');

        return view('stock-entries.create', compact('products', 'suppliers', 'selectedProductId'));
    }

    /**
     * Store a newly created resource in storage.
     * RF01, RF02, RF03: Registrar entrada y actualizar stock
     */
    public function store(Request $request)
    {
        // Validaciones
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'entry_date' => 'required|date|before_or_equal:today',
            'document_type' => 'nullable|in:factura,boleta,nota_entrada,nota_salida,guia_remision,otros',
            'document_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'product_id.required' => 'Debe seleccionar un producto',
            'product_id.exists' => 'El producto seleccionado no existe',
            'supplier_id.required' => 'Debe seleccionar un proveedor',
            'supplier_id.exists' => 'El proveedor seleccionado no existe',
            'quantity.required' => 'La cantidad es obligatoria',
            'quantity.min' => 'La cantidad debe ser mayor a 0',
            'entry_date.required' => 'La fecha de entrada es obligatoria',
            'entry_date.before_or_equal' => 'La fecha no puede ser futura',
        ]);

        // RF02: Verificar que el producto existe y está activo
        $product = Product::findOrFail($validated['product_id']);
        if ($product->status !== 'active') {
            return back()->withErrors(['product_id' => 'El producto está inactivo.'])->withInput();
        }

        // Añadir el usuario autenticado
        $validated['user_id'] = Auth::id();

        // Crear la entrada (RF03: El stock se actualiza automáticamente en el modelo)
        $entry = StockEntry::create($validated);

        return redirect()->route('stock-entries.index')
            ->with('success', "Entrada registrada exitosamente. Nuevo stock: {$product->fresh()->current_stock} {$product->unit->abbreviation}");
    }

    /**
     * Display the specified resource.
     */
    public function show(StockEntry $stockEntry)
    {
        $stockEntry->load(['product.category', 'product.unit', 'supplier', 'user']);
        
        return view('stock-entries.show', compact('stockEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockEntry $stockEntry)
    {
        $products = Product::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        
        return view('stock-entries.edit', compact('stockEntry', 'products', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockEntry $stockEntry)
    {
        // Validaciones
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'entry_date' => 'required|date|before_or_equal:today',
            'document_type' => 'nullable|in:factura,boleta,nota_entrada,nota_salida,guia_remision,otros',
            'document_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'supplier_id.required' => 'Debe seleccionar un proveedor',
            'entry_date.required' => 'La fecha de entrada es obligatoria',
            'entry_date.before_or_equal' => 'La fecha no puede ser futura',
        ]);

        // NOTA: No permitimos cambiar el producto ni la cantidad 
        // para mantener la integridad del historial de stock

        $stockEntry->update($validated);

        return redirect()->route('stock-entries.index')
            ->with('success', 'Entrada actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockEntry $stockEntry)
    {
        // IMPORTANTE: Eliminar una entrada es delicado porque afecta el stock
        // Solo permitir si es la última entrada del producto
        
        $product = $stockEntry->product;
        $lastEntry = $product->stockEntries()->latest('created_at')->first();
        
        if ($lastEntry->id !== $stockEntry->id) {
            return redirect()->route('stock-entries.index')
                ->with('error', 'Solo se puede eliminar la última entrada registrada del producto.');
        }

        // Revertir el stock manualmente antes de eliminar
        $product->decrement('current_stock', $stockEntry->quantity);
        
        // Eliminar el movimiento relacionado
        $stockEntry->product->stockMovements()
            ->where('reference_id', $stockEntry->id)
            ->where('reference_type', get_class($stockEntry))
            ->delete();
        
        // Eliminar la entrada
        $stockEntry->delete();

        return redirect()->route('stock-entries.index')
            ->with('success', 'Entrada eliminada exitosamente. Stock actualizado.');
    }
}