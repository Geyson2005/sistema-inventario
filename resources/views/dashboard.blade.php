@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            <p class="text-muted">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <!-- Total de Productos -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total de Productos</h6>
                            <h2 class="mb-0">{{ $totalProducts }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Stock Bajo</h6>
                            <h2 class="mb-0 text-warning">{{ $lowStockProducts }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos Agotados -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Productos Agotados</h6>
                            <h2 class="mb-0 text-danger">{{ $outOfStockProducts }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movimientos del Mes -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Entradas del Mes</h6>
                            <h2 class="mb-0 text-success">{{ $entriesThisMonth }}</h2>
                            <small class="text-muted">Salidas: {{ $exitsThisMonth }}</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Lotes -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-down text-success fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $entryBatchesThisMonth }}</h3>
                    <p class="text-muted mb-0">Entradas Múltiples este Mes</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-up text-danger fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $exitBatchesThisMonth }}</h3>
                    <p class="text-muted mb-0">Salidas Múltiples este Mes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Alertas de Stock -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bell"></i> Alertas de Stock
                </div>
                <div class="card-body">
                    @if($alertProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertProducts as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ $product->code }}</small>
                                            </td>
                                            <td>
                                                {{ $product->current_stock }} {{ $product->unit->abbreviation }}<br>
                                                <small class="text-muted">Mín: {{ $product->min_stock }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->stock_status_color }}">
                                                    {{ $product->stock_status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> No hay alertas de stock en este momento.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Últimas Entradas Múltiples -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-arrow-down"></i> Últimas Entradas Múltiples
                </div>
                <div class="card-body">
                    @if($recentEntryBatches->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentEntryBatches as $batch)
                                <a href="{{ route('stock-entry-batches.show', $batch) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $batch->supplier->name }}</h6>
                                        <small>{{ $batch->entry_date->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">
                                        <span class="badge bg-primary">{{ $batch->total_items }} productos</span>
                                        <span class="badge bg-success">+{{ $batch->total_quantity }} unidades</span>
                                    </p>
                                    @if($batch->document_number)
                                        <small class="text-muted">{{ $batch->document_type_label }} {{ $batch->document_number }}</small>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No hay entradas múltiples registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Últimas Salidas Múltiples -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-arrow-up"></i> Últimas Salidas Múltiples
                </div>
                <div class="card-body">
                    @if($recentExitBatches->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentExitBatches as $batch)
                                <a href="{{ route('stock-exit-batches.show', $batch) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $batch->reason_label }}</h6>
                                        <small>{{ $batch->exit_date->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">
                                        <span class="badge bg-primary">{{ $batch->total_items }} productos</span>
                                        <span class="badge bg-danger">-{{ $batch->total_quantity }} unidades</span>
                                    </p>
                                    @if($batch->document_number)
                                        <small class="text-muted">{{ $batch->document_type_label }} {{ $batch->document_number }}</small>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No hay salidas múltiples registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Movimientos -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history"></i> Últimos Movimientos de Stock
                </div>
                <div class="card-body">
                    @if($recentMovements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Stock</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMovements as $movement)
                                        <tr>
                                            <td>
                                                <small>{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $movement->product->name }}</strong>
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
                                            </td>
                                            <td>
                                                {{ $movement->previous_stock }} → <strong>{{ $movement->new_stock }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ $movement->user->name }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No hay movimientos registrados aún.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection