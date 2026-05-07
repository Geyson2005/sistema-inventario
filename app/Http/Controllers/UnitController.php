<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unit::withCount('products');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        // Ordenar por nombre
        $units = $query->orderBy('name', 'asc')->paginate(15);

        return view('units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name',
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Esta unidad ya existe',
            'name.max' => 'El nombre no puede exceder 50 caracteres',
            'abbreviation.required' => 'La abreviatura es obligatoria',
            'abbreviation.unique' => 'Esta abreviatura ya existe',
            'abbreviation.max' => 'La abreviatura no puede exceder 10 caracteres',
        ]);

        // Convertir abreviatura a mayúsculas
        $validated['abbreviation'] = strtoupper($validated['abbreviation']);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $unit->loadCount('products');
        
        // Productos con esta unidad
        $products = $unit->products()
            ->with('category')
            ->orderBy('name')
            ->paginate(20);

        return view('units.show', compact('unit', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $unit->id,
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation,' . $unit->id,
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Esta unidad ya existe',
            'name.max' => 'El nombre no puede exceder 50 caracteres',
            'abbreviation.required' => 'La abreviatura es obligatoria',
            'abbreviation.unique' => 'Esta abreviatura ya existe',
            'abbreviation.max' => 'La abreviatura no puede exceder 10 caracteres',
        ]);

        // Convertir abreviatura a mayúsculas
        $validated['abbreviation'] = strtoupper($validated['abbreviation']);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Verificar si tiene productos asociados
        if ($unit->products()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'No se puede eliminar esta unidad porque tiene productos asociados.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unidad de medida eliminada exitosamente.');
    }
}