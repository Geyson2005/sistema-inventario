<?php

namespace App\Http\Controllers;

use App\Models\StockEntryBatch;
use App\Models\StockEntry;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockEntryBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockEntryBatch::with(['supplier', 'user', 'entries']);

        // Filtro por búsqueda (proveedor o documento)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
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
        $batches = $query->orderBy('entry_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        // Para los filtros
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('stock-entries.batches.index', compact('batches', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()->with(['unit', 'category'])->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('stock-entries.batches.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar datos del lote
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'entry_date' => 'required|date|before_or_equal:today',
            'document_type' => 'nullable|in:factura,boleta,nota_entrada,nota_salida,guia_remision,otros',
            'document_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            
            // Validar que haya al menos un producto
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'supplier_id.required' => 'Debe seleccionar un proveedor',
            'entry_date.required' => 'La fecha de entrada es obligatoria',
            'entry_date.before_or_equal' => 'La fecha no puede ser futura',
            'products.required' => 'Debe añadir al menos un producto',
            'products.min' => 'Debe añadir al menos un producto',
            'products.*.product_id.required' => 'Producto inválido',
            'products.*.quantity.required' => 'La cantidad es obligatoria',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a 0',
        ]);

        DB::beginTransaction();
        
        try {
            // Crear el lote
            $batch = StockEntryBatch::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'entry_date' => $request->entry_date,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'notes' => $request->notes,
            ]);

            // Crear las entradas individuales
            foreach ($request->products as $productData) {
                // Verificar que el producto esté activo
                $product = Product::findOrFail($productData['product_id']);
                
                if ($product->status !== 'active') {
                    throw new \Exception("El producto {$product->name} está inactivo.");
                }

                // Crear la entrada
                StockEntry::create([
                    'batch_id' => $batch->id,
                    'product_id' => $productData['product_id'],
                    'supplier_id' => $request->supplier_id,
                    'user_id' => Auth::id(),
                    'quantity' => $productData['quantity'],
                    'entry_date' => $request->entry_date,
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'notes' => $request->notes,
                ]);
            }

            // Calcular totales del lote
            $batch->calculateTotals();

            DB::commit();

            return redirect()->route('stock-entry-batches.show', $batch)
                ->with('success', "Entrada múltiple registrada exitosamente. Total: {$batch->total_items} productos, {$batch->total_quantity} unidades.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Error al registrar la entrada: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockEntryBatch $stockEntryBatch)
    {
        $stockEntryBatch->load(['supplier', 'user', 'entries.product.unit']);

        return view('stock-entries.batches.show', compact('stockEntryBatch'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockEntryBatch $stockEntryBatch)
    {
        // Verificar que sea el último lote
        $lastBatch = StockEntryBatch::latest('created_at')->first();
        
        if ($lastBatch->id !== $stockEntryBatch->id) {
            return redirect()->route('stock-entry-batches.index')
                ->with('error', 'Solo se puede eliminar el último lote registrado.');
        }

        DB::beginTransaction();
        
        try {
            // Revertir el stock de todos los productos
            foreach ($stockEntryBatch->entries as $entry) {
                $product = $entry->product;
                $product->decrement('current_stock', $entry->quantity);
                
                // Eliminar el movimiento relacionado
                $product->stockMovements()
                    ->where('reference_id', $entry->id)
                    ->where('reference_type', StockEntry::class)
                    ->delete();
            }

            // Eliminar el lote (esto eliminará automáticamente las entradas por cascade)
            $stockEntryBatch->delete();

            DB::commit();

            return redirect()->route('stock-entry-batches.index')
                ->with('success', 'Lote de entrada eliminado exitosamente. Stock revertido.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('stock-entry-batches.index')
                ->with('error', 'Error al eliminar el lote: ' . $e->getMessage());
        }
    }
}