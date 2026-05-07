@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
            <p class="text-muted">Administrar clientes del sistema</p>
        </div>
        <div class="col-md-6 text-end">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nuevo Cliente
                </a>
            @endif
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Código, nombre, documento o email..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Tipo de Documento</label>
                    <select name="document_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="DNI" {{ request('document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="RUC" {{ request('document_type') == 'RUC' ? 'selected' : '' }}>RUC</option>
                        <option value="CE" {{ request('document_type') == 'CE' ? 'selected' : '' }}>CE</option>
                        <option value="Pasaporte" {{ request('document_type') == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Dirección</th>
                            <th>Compras</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td><strong>{{ $client->code }}</strong></td>
                                <td>{{ $client->name }}</td>
                                <td>
                                    @if($client->document_number)
                                        <span class="badge bg-dark">{{ $client->document_type }}</span><br>
                                        <small>{{ $client->formatted_document }}</small>
                                    @else
                                        <span class="text-muted">Sin documento</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if($client->phone)
                                        <div><i class="fas fa-phone text-primary"></i> {{ $client->phone }}</div>
                                    @endif
                                    @if($client->email)
                                        <div><i class="fas fa-envelope text-info"></i> {{ $client->email }}</div>
                                    @endif
                                    @if(!$client->phone && !$client->email)
                                        <span class="text-muted">Sin contacto</span>
                                    @endif
                                </td>
                                <td class="small">{{ Str::limit($client->address, 40) ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $client->stock_exit_batches_count }} compras
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $client->status_color }}">
                                        {{ $client->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clients.show', $client) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('clients.edit', $client) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('clients.destroy', $client) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este cliente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Eliminar"
                                                        {{ $client->stock_exit_batches_count > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron clientes</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $clients->links() }}
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6>Total Clientes</h6>
                    <h3>{{ $clients->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6>Activos</h6>
                    <h3>{{ \App\Models\Client::active()->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6>Inactivos</h6>
                    <h3>{{ \App\Models\Client::inactive()->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6>Con RUC</h6>
                    <h3>{{ \App\Models\Client::where('document_type', 'RUC')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection