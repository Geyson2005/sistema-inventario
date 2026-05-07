@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-box"></i> Detalles del Producto</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                    <li class="breadcrumb-item active">{{ $product->code }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Producto -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle"></i> Información General
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Código:</strong></td>
                            <td>{{ $product->code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Categoría:</strong></td>
                            <td>
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Unidad:</strong></td>
                            <td>{{ $product->unit->name }} ({{ $product->unit->abbreviation }})</td>
                        </tr>
                        <tr>
                            <td><strong>Descripción:</strong></td>
                            <td>{{ $product->description ?? 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Registrado:</strong></td>
                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Producto
                            </a>
                        @endif
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Actual -->
        <div class="col-md-4 mb-4">
            <div class="card stat-card {{ $product->stock_status }}">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Stock Actual</h6>
                    <h1 class="display-3 mb-0">{{ $product->current_stock }}</h1>
                    <p class="text-muted mb-3">{{ $product->unit->abbreviation }}</p>
                    
                    <span class="badge bg-{{ $product->stock_status_color }} fs-6">
                        {{ $product->stock_status_label }}
                    </span>

                    <hr class="my-3">

                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Stock Mínimo:</small><br>
                            <strong>{{ $product->min_stock }} {{ $product->unit->abbreviation }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Diferencia:</small><br>
                            <strong class="text-{{ $product->current_stock >= $product->min_stock ? 'success' : 'danger' }}">
                                {{ $product->current_stock - $product->min_stock }} {{ $product->unit->abbreviation }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Acciones Rápidas
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('stock-entries.create') }}?product_id={{ $product->id }}" 
                           class="btn btn-success">
                            <i class="fas fa-arrow-down"></i> Registrar Entrada
                        </a>
                        <a href="{{ route('stock-exits.create') }}?product_id={{ $product->id }}" 
                           class="btn btn-danger">
                            <i class="fas fa-arrow-up"></i> Registrar Salida
                        </a>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('stock-adjustments.create') }}?product_id={{ $product->id }}" 
                               class="btn btn-warning">
                                <i class="fas fa-tools"></i> Ajustar Stock
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Movimientos -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i> Resumen de Movimientos
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-arrow-down text-success"></i> Total Entradas:</span>
                            <strong class="text-success">
                                {{ $product->stockEntries->sum('quantity') }} {{ $product->unit->abbreviation }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-arrow-up text-danger"></i> Total Salidas:</span>
                            <strong class="text-danger">
                                {{ $product->stockExits->sum('quantity') }} {{ $product->unit->abbreviation }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-tools text-warning"></i> Total Ajustes:</span>
                            <strong class="text-warning">
                                {{ $product->stockAdjustments->count() }}
                            </strong>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Movimientos:</strong></span>
                        <strong class="text-primary">{{ $product->stockMovements->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial Completo -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history"></i> Historial Completo de Movimientos
                </div>
                <div class="card-body">
                    @if($movements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Stock Anterior</th>
                                        <th>Stock Nuevo</th>
                                        <th>Usuario</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                        <tr>
                                            <td>
                                                <small>{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $movement->type_color }}">
                                                    {{ $movement->type_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($movement->movement_type === 'entry')
                                                    <span class="text-success fw-bold">+{{ $movement->quantity }}</span>
                                                @else
                                                    <span class="text-danger fw-bold">-{{ $movement->quantity }}</span>
                                                @endif
                                                {{ $product->unit->abbreviation }}
                                            </td>
                                            <td>{{ $movement->previous_stock }} {{ $product->unit->abbreviation }}</td>
                                            <td>
                                                <strong>{{ $movement->new_stock }} {{ $product->unit->abbreviation }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ $movement->user->name }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $movement->notes ?? '-' }}</small>
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
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Este producto aún no tiene movimientos registrados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection