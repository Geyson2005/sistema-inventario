@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-ruler"></i> Gestión de Unidades de Medida</h2>
            <p class="text-muted">Administrar unidades de medida para productos</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Unidad
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
            <form method="GET" action="{{ route('units.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nombre o abreviatura..." 
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('units.index') }}" class="btn btn-secondary">
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
                            <th width="10%">#</th>
                            <th width="40%">Nombre</th>
                            <th width="20%">Abreviatura</th>
                            <th width="20%">Productos</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td><strong>{{ $unit->name }}</strong></td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $unit->abbreviation }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $unit->products_count }} productos
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('units.show', $unit) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver productos">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('units.edit', $unit) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('units.destroy', $unit) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar esta unidad?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Eliminar"
                                                    {{ $unit->products_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron unidades de medida</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $units->links() }}
            </div>
        </div>
    </div>

    <!-- Panel Informativo -->
    <div class="card mt-4 bg-light">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> Unidades Comunes</h5>
            <div class="row mt-3">
                <div class="col-md-3">
                    <h6>Peso</h6>
                    <ul class="small">
                        <li>Kilogramo (KG)</li>
                        <li>Gramo (GR)</li>
                        <li>Tonelada (TN)</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Volumen</h6>
                    <ul class="small">
                        <li>Litro (LT)</li>
                        <li>Mililitro (ML)</li>
                        <li>Galón (GAL)</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Longitud</h6>
                    <ul class="small">
                        <li>Metro (MT)</li>
                        <li>Centímetro (CM)</li>
                        <li>Pulgada (IN)</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Cantidad</h6>
                    <ul class="small">
                        <li>Unidad (UN)</li>
                        <li>Docena (DOC)</li>
                        <li>Caja (CJ)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection