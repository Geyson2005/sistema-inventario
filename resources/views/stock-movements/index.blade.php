@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-history"></i> Historial de Movimientos</h2>
            <p class="text-muted">Registro completo de todos los movimientos de stock del sistema</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('stock-movements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar Producto</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Código o nombre..." 
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
                    <label class="form-label">Categoría</label>
                    <select name="category_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipo Movimiento</label>
                    <select name="movement_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="entry" {{ request('movement_type') == 'entry' ? 'selected' : '' }}>
                            Entrada
                        </option>
                        <option value="exit" {{ request('movement_type') == 'exit' ? 'selected' : '' }}>
                            Salida
                        </option>
                        <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>
                            Ajuste
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>

                <!-- Segunda fila de filtros -->
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

    <!-- Resumen -->
    @if($movements->total() > 0)
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> 
            Se encontraron <strong>{{ $movements->total() }}</strong> movimientos
            @if(request('start_date') && request('end_date'))
                entre {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} 
                y {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
            @endif
        </div>
    @endif

    <!-- Tabla -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Usuario</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>
                                    <strong>{{ $movement->created_at->format('d/m/Y') }}</strong><br>
                                    <small class="text-muted">{{ $movement->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('stock-movements.show', $movement->product) }}" 
                                       class="text-decoration-none">
                                        <strong>{{ $movement->product->name }}</strong>
                                    </a><br>
                                    <small class="text-muted">
                                        {{ $movement->product->code }} | {{ $movement->product->category->name }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $movement->type_color }}">
                                        {{ $movement->type_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($movement->movement_type === 'entry')
                                        <span class="text-success fw-bold">
                                            +{{ $movement->quantity }} {{ $movement->product->unit->abbreviation }}
                                        </span>
                                    @elseif($movement->movement_type === 'exit')
                                        <span class="text-danger fw-bold">
                                            -{{ $movement->quantity }} {{ $movement->product->unit->abbreviation }}
                                        </span>
                                    @else
                                        <span class="text-warning fw-bold">
                                            {{ $movement->quantity }} {{ $movement->product->unit->abbreviation }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $movement->previous_stock }} {{ $movement->product->unit->abbreviation }}</td>
                                <td>
                                    <strong>{{ $movement->new_stock }} {{ $movement->product->unit->abbreviation }}</strong>
                                </td>
                                <td>
                                    <small>{{ $movement->user->name }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($movement->notes, 40) }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron movimientos con los filtros aplicados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection