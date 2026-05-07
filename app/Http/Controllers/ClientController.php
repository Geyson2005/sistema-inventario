<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::withCount(['stockExits', 'stockExitBatches']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
        }

        // Filtro por tipo de documento
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por nombre
        $clients = $query->orderBy('name', 'asc')->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:clients,code',
            'name' => 'required|string|max:150',
            'document_type' => 'nullable|in:DNI,RUC,CE,Pasaporte,Otro',
            'document_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado',
            'name.required' => 'El nombre es obligatorio',
            'email.email' => 'Ingrese un email válido',
            'status.required' => 'Debe seleccionar un estado',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->loadCount(['stockExits', 'stockExitBatches']);
        
        // Últimas salidas/compras de este cliente
        $recentPurchases = $client->stockExitBatches()
            ->with(['user'])
            ->orderBy('exit_date', 'desc')
            ->paginate(10);

        // Estadísticas
        $stats = [
            'total_purchases' => $client->stockExitBatches()->count(),
            'total_exits' => $client->stockExits()->count(),
            'total_quantity' => $client->stockExits()->sum('quantity'),
        ];

        return view('clients.show', compact('client', 'recentPurchases', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:clients,code,' . $client->id,
            'name' => 'required|string|max:150',
            'document_type' => 'nullable|in:DNI,RUC,CE,Pasaporte,Otro',
            'document_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado',
            'name.required' => 'El nombre es obligatorio',
            'email.email' => 'Ingrese un email válido',
            'status.required' => 'Debe seleccionar un estado',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Verificar si tiene salidas registradas
        if (!$client->canDelete()) {
            return redirect()->route('clients.index')
                ->with('error', 'No se puede eliminar este cliente porque tiene salidas de stock registradas.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Search clients (AJAX)
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $clients = Client::active()
            ->where(function($query) use ($term) {
                $query->where('code', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('document_number', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json($clients);
    }
}