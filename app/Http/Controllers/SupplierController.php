<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Filtro por búsqueda (nombre o RUC)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ruc', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por nombre
        $suppliers = $query->orderBy('name', 'asc')->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'ruc' => 'nullable|string|size:11|unique:suppliers,ruc',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'El nombre del proveedor es obligatorio',
            'ruc.size' => 'El RUC debe tener exactamente 11 dígitos',
            'ruc.unique' => 'Este RUC ya está registrado',
            'email.email' => 'El formato del correo no es válido',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Cargar relaciones
        $supplier->load(['stockEntries.product', 'stockEntries.user']);
        
        // Últimas 20 entradas del proveedor
        $entries = $supplier->stockEntries()
            ->with(['product', 'user'])
            ->orderBy('entry_date', 'desc')
            ->paginate(20);

        // Estadísticas
        $totalEntries = $supplier->stockEntries()->count();
        $totalQuantity = $supplier->stockEntries()->sum('quantity');
        
        // Productos más comprados a este proveedor
        $topProducts = $supplier->stockEntries()
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('product')
            ->get();

        return view('suppliers.show', compact('supplier', 'entries', 'totalEntries', 'totalQuantity', 'topProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'ruc' => 'nullable|string|size:11|unique:suppliers,ruc,' . $supplier->id,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'El nombre del proveedor es obligatorio',
            'ruc.size' => 'El RUC debe tener exactamente 11 dígitos',
            'ruc.unique' => 'Este RUC ya está registrado',
            'email.email' => 'El formato del correo no es válido',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Verificar si tiene entradas
        if (!$supplier->canDelete()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'No se puede eliminar este proveedor porque tiene entradas registradas.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}