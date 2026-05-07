@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-arrow-down text-success"></i> Gestión de Entradas</h2>
            <p class="text-muted">Registro de entradas de productos al almacén</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('stock-entries.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Entrada
            </a>
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
            <form method="GET" action="{{ route('stock-entries.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Producto o proveedor..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Producto</label>
                    <select name="product_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Proveedor</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipo Documento</label>
                    <select name="document_type" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\StockEntry::getDocumentTypeLabels() as $key => $label)
                            <option value="{{ $key }}" 
                                {{ request('document_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('stock-entries.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>

                <!-- Filtros de Fecha (Segunda fila) -->
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ request('end_date') }}">
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
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Proveedor</th>
                            <th>Cantidad</th>
                            <th>Documento</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>
                                    <strong>{{ $entry->entry_date->format('d/m/Y') }}</strong><br>
                                    <small class="text-muted">{{ $entry->entry_date->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <strong>{{ $entry->product->name }}</strong><br>
                                    <small class="text-muted">{{ $entry->product->code }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $entry->supplier->name }}</span>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">
                                        +{{ $entry->quantity }} {{ $entry->product->unit->abbreviation }}
                                    </span>
                                </td>
                                <td>
                                    @if($entry->document_type)
                                        <span class="badge bg-info">{{ $entry->document_type_label }}</span><br>
                                        <small>{{ $entry->document_number }}</small>
                                    @else
                                        <span class="text-muted">Sin documento</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $entry->user->name }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('stock-entries.show', $entry) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('stock-entries.edit', $entry) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('stock-entries.destroy', $entry) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('⚠️ ADVERTENCIA: Eliminar esta entrada revertirá el stock del producto.\n¿Está seguro de continuar?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron entradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection