<?php

namespace App\Http\Controllers;

use App\Models\StockExitBatch;
use App\Models\StockExit;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockExitBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockExitBatch::with(['user', 'exits']);

        // Filtro por búsqueda (documento)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('document_number', 'like', "%{$search}%");
        }

        // Filtro por motivo
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Filtro por tipo de documento
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->where('exit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('exit_date', '<=', $request->end_date);
        }

        // Ordenar por fecha más reciente
        $batches = $query->orderBy('exit_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return view('stock-exits.batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()
            ->where('current_stock', '>', 0)
            ->with(['unit', 'category'])
            ->orderBy('name')
            ->get();
            
            $clients = Client::active()->orderBy('name')->get();

        return view('stock-exits.batches.create', compact('products', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar datos del lote
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'exit_date' => 'required|date|before_or_equal:today',
            'reason' => 'required|in:sale,transfer,damage,other',
            'document_type' => 'nullable|in:factura,boleta,nota_entrada,nota_salida,guia_remision,otros',
            'document_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            
            // Validar que haya al menos un producto
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'exit_date.required' => 'La fecha de salida es obligatoria',
            'exit_date.before_or_equal' => 'La fecha no puede ser futura',
            'client_id.exists' => 'El cliente seleccionado no existe',
            'reason.required' => 'Debe seleccionar el motivo de salida',
            'products.required' => 'Debe añadir al menos un producto',
            'products.min' => 'Debe añadir al menos un producto',
            'products.*.product_id.required' => 'Producto inválido',
            'products.*.quantity.required' => 'La cantidad es obligatoria',
            'products.*.quantity.min' => 'La cantidad debe ser mayor a 0',
        ]);

        DB::beginTransaction();
        
        try {
            // Validar stock disponible ANTES de crear el lote
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                if ($product->status !== 'active') {
                    throw new \Exception("El producto {$product->name} está inactivo.");
                }

                if ($product->current_stock < $productData['quantity']) {
                    throw new \Exception("Stock insuficiente para {$product->name}. Disponible: {$product->current_stock} {$product->unit->abbreviation}");
                }
            }

            // Crear el lote
            $batch = StockExitBatch::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'exit_date' => $request->exit_date,
                'reason' => $request->reason,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'notes' => $request->notes,
            ]);

            // Crear las salidas individuales
            foreach ($request->products as $productData) {
                StockExit::create([
                    'batch_id' => $batch->id,
                    'product_id' => $productData['product_id'],
                    'user_id' => Auth::id(),
                    'quantity' => $productData['quantity'],
                    'exit_date' => $request->exit_date,
                    'reason' => $request->reason,
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'notes' => $request->notes,
                ]);
            }

            // Calcular totales del lote
            $batch->calculateTotals();

            DB::commit();

            return redirect()->route('stock-exit-batches.show', $batch)
                ->with('success', "Salida múltiple registrada exitosamente. Total: {$batch->total_items} productos, {$batch->total_quantity} unidades.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Error al registrar la salida: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockExitBatch $stockExitBatch)
    {
        $stockExitBatch->load(['user', 'exits.product.unit']);

        return view('stock-exits.batches.show', compact('stockExitBatch'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockExitBatch $stockExitBatch)
    {
        // Verificar que sea el último lote
        $lastBatch = StockExitBatch::latest('created_at')->first();
        
        if ($lastBatch->id !== $stockExitBatch->id) {
            return redirect()->route('stock-exit-batches.index')
                ->with('error', 'Solo se puede eliminar el último lote registrado.');
        }

        DB::beginTransaction();
        
        try {
            // Revertir el stock de todos los productos
            foreach ($stockExitBatch->exits as $exit) {
                $product = $exit->product;
                $product->increment('current_stock', $exit->quantity);
                
                // Eliminar el movimiento relacionado
                $product->stockMovements()
                    ->where('reference_id', $exit->id)
                    ->where('reference_type', StockExit::class)
                    ->delete();
            }

            // Eliminar el lote (esto eliminará automáticamente las salidas por cascade)
            $stockExitBatch->delete();

            DB::commit();

            return redirect()->route('stock-exit-batches.index')
                ->with('success', 'Lote de salida eliminado exitosamente. Stock revertido.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('stock-exit-batches.index')
                ->with('error', 'Error al eliminar el lote: ' . $e->getMessage());
        }
    }
}