@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-up text-danger"></i> Detalles de la Salida Múltiple</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-exit-batches.index') }}">Salidas</a></li>
                    <li class="breadcrumb-item active">Lote #{{ $stockExitBatch->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Lote -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-info-circle"></i> Información del Lote
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Lote:</strong></td>
                            <td>#{{ $stockExitBatch->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Salida:</strong></td>
                            <td>
                                {{ $stockExitBatch->exit_date->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $stockExitBatch->exit_date->diffForHumans() }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Motivo:</strong></td>
                            <td>
                                <span class="badge bg-warning fs-6">{{ $stockExitBatch->reason_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cliente:</strong></td>
                            <td>
                                @if($stockExitBatch->client)
                                    <strong>{{ $stockExitBatch->client->name }}</strong><br>
                                    <small class="text-muted">
                                        {{ $stockExitBatch->client->code }}
                                        @if($stockExitBatch->client->document_number)
                                            | {{ $stockExitBatch->client->document_type }}:
                                            {{ $stockExitBatch->client->formatted_document }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">Sin cliente asignado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tipo Documento:</strong></td>
                            <td>
                                @if($stockExitBatch->document_type)
                                    <span class="badge bg-info">{{ $stockExitBatch->document_type_label }}</span>
                                @else
                                    <span class="text-muted">Sin documento</span>
                                @endif
                            </td>
                        </tr>
                        @if($stockExitBatch->document_number)
                            <tr>
                                <td><strong>N° Documento:</strong></td>
                                <td><strong>{{ $stockExitBatch->document_number }}</strong></td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Registrado por:</strong></td>
                            <td>
                                {{ $stockExitBatch->user->name }}<br>
                                <small class="text-muted">{{ $stockExitBatch->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                        </tr>
                    </table>

                    @if($stockExitBatch->notes)
                        <hr>
                        <h6><i class="fas fa-sticky-note"></i> Notas</h6>
                        <div class="alert alert-light">
                            {{ $stockExitBatch->notes }}
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <form action="{{ route('stock-exit-batches.destroy', $stockExitBatch) }}" 
                                  method="POST"
                                  onsubmit="return confirm('⚠️ ADVERTENCIA: Esto eliminará TODAS las salidas de este lote y revertirá el stock.\n¿Está seguro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> Eliminar Lote
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('stock-exit-batches.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen del Lote -->
        <div class="col-md-8 mb-4">
            <div class="row mb-3">
                <!-- Total de Productos -->
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total de Productos Diferentes</h6>
                            <h1 class="display-4 mb-0 text-primary">{{ $stockExitBatch->total_items }}</h1>
                        </div>
                    </div>
                </div>

                <!-- Total de Unidades -->
                <div class="col-md-6">
                    <div class="card stat-card danger">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total de Unidades Retiradas</h6>
                            <h1 class="display-4 mb-0 text-danger">{{ $stockExitBatch->total_quantity }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-boxes"></i> Productos Retirados en este Lote
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Cantidad</th>
                                    <th>Stock Actual</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockExitBatch->exits as $exit)
                                    <tr>
                                        <td>
                                            <strong>{{ $exit->product->name }}</strong><br>
                                            <small class="text-muted">{{ $exit->product->code }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ optional($exit->product->category)->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">
                                                -{{ $exit->quantity }} {{ $exit->product->unit->abbreviation }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $exit->product->current_stock }}</strong> {{ $exit->product->unit->abbreviation }}<br>
                                            <span class="badge bg-{{ $exit->product->stock_status_color }}">
                                                {{ $exit->product->stock_status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $exit->product) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2"><strong>TOTALES:</strong></td>
                                    <td><strong class="text-danger">-{{ $stockExitBatch->total_quantity }} unidades</strong></td>
                                    <td colspan="2"><strong>{{ $stockExitBatch->total_items }} productos</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
