@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-history"></i> Historial del Producto</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-movements.index') }}">Historial</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Producto -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-box"></i> Información del Producto
                </div>
                <div class="card-body">
                    <h5 class="mb-3">{{ $product->name }}</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Código:</strong></td>
                            <td>{{ $product->code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Categoría:</strong></td>
                            <td>{{ $product->category->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Unidad:</strong></td>
                            <td>{{ $product->unit->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Stock Actual:</strong></td>
                            <td>
                                <strong class="fs-5">{{ $product->current_stock }}</strong> 
                                {{ $product->unit->abbreviation }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Stock Mínimo:</strong></td>
                            <td>{{ $product->min_stock }} {{ $product->unit->abbreviation }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                <span class="badge bg-{{ $product->stock_status_color }}">
                                    {{ $product->stock_status_label }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver Producto
                        </a>
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-8 mb-4">
            <div class="row mb-3">
                <!-- Total Entradas -->
                <div class="col-md-4">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Entradas</h6>
                            <h2 class="mb-0 text-success">+{{ $totalEntries }}</h2>
                            <small class="text-muted">{{ $product->unit->abbreviation }}</small>
                        </div>
                    </div>
                </div>

                <!-- Total Salidas -->
                <div class="col-md-4">
                    <div class="card stat-card danger">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Salidas</h6>
                            <h2 class="mb-0 text-danger">-{{ $totalExits }}</h2>
                            <small class="text-muted">{{ $product->unit->abbreviation }}</small>
                        </div>
                    </div>
                </div>

                <!-- Total Ajustes -->
                <div class="col-md-4">
                    <div class="card stat-card warning">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Ajustes</h6>
                            <h2 class="mb-0 text-warning">{{ $totalAdjustments }}</h2>
                            <small class="text-muted">registros</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Movimientos -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list"></i> Historial Completo de Movimientos
                    <span class="badge bg-primary">{{ $movements->total() }} registros</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Stock</th>
                                    <th>Usuario</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr>
                                        <td>
                                            <strong>{{ $movement->created_at->format('d/m/Y') }}</strong><br>
                                            <small class="text-muted">{{ $movement->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type_color }}">
                                                {{ $movement->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($movement->movement_type === 'entry')
                                                <span class="text-success fw-bold">
                                                    +{{ $movement->quantity }} {{ $product->unit->abbreviation }}
                                                </span>
                                            @elseif($movement->movement_type === 'exit')
                                                <span class="text-danger fw-bold">
                                                    -{{ $movement->quantity }} {{ $product->unit->abbreviation }}
                                                </span>
                                            @else
                                                <span class="text-warning fw-bold">
                                                    {{ $movement->quantity }} {{ $product->unit->abbreviation }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $movement->previous_stock }} →</small>
                                            <strong>{{ $movement->new_stock }}</strong>
                                            {{ $product->unit->abbreviation }}
                                        </td>
                                        <td>
                                            <small>{{ $movement->user->name }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $movement->notes }}</small>
                                        </td>
                                    </tr>
                                @endforeach
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
    </div>
</div>
@endsection