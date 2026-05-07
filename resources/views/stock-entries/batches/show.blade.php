@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-down text-success"></i> Detalles de la Entrada Múltiple</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-entry-batches.index') }}">Entradas</a></li>
                    <li class="breadcrumb-item active">Lote #{{ $stockEntryBatch->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Lote -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-info-circle"></i> Información del Lote
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Lote:</strong></td>
                            <td>#{{ $stockEntryBatch->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Entrada:</strong></td>
                            <td>
                                {{ $stockEntryBatch->entry_date->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $stockEntryBatch->entry_date->diffForHumans() }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Proveedor:</strong></td>
                            <td>
                                <a href="{{ route('suppliers.show', $stockEntryBatch->supplier) }}" class="text-decoration-none">
                                    <span class="badge bg-secondary fs-6">
                                        {{ $stockEntryBatch->supplier->name }}
                                    </span>
                                </a>
                                @if($stockEntryBatch->supplier->ruc)
                                    <br><small class="text-muted">RUC: {{ $stockEntryBatch->supplier->ruc }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tipo Documento:</strong></td>
                            <td>
                                @if($stockEntryBatch->document_type)
                                    <span class="badge bg-info">{{ $stockEntryBatch->document_type_label }}</span>
                                @else
                                    <span class="text-muted">Sin documento</span>
                                @endif
                            </td>
                        </tr>
                        @if($stockEntryBatch->document_number)
                            <tr>
                                <td><strong>N° Documento:</strong></td>
                                <td><strong>{{ $stockEntryBatch->document_number }}</strong></td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Registrado por:</strong></td>
                            <td>
                                {{ $stockEntryBatch->user->name }}<br>
                                <small class="text-muted">{{ $stockEntryBatch->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                        </tr>
                    </table>

                    @if($stockEntryBatch->notes)
                        <hr>
                        <h6><i class="fas fa-sticky-note"></i> Notas</h6>
                        <div class="alert alert-light">
                            {{ $stockEntryBatch->notes }}
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <form action="{{ route('stock-entry-batches.destroy', $stockEntryBatch) }}" 
                                  method="POST"
                                  onsubmit="return confirm('⚠️ ADVERTENCIA: Esto eliminará TODAS las entradas de este lote y revertirá el stock.\n¿Está seguro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> Eliminar Lote
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('stock-entry-batches.index') }}" class="btn btn-secondary">
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
                            <h1 class="display-4 mb-0 text-primary">{{ $stockEntryBatch->total_items }}</h1>
                        </div>
                    </div>
                </div>

                <!-- Total de Unidades -->
                <div class="col-md-6">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total de Unidades Ingresadas</h6>
                            <h1 class="display-4 mb-0 text-success">{{ $stockEntryBatch->total_quantity }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-boxes"></i> Productos Ingresados en este Lote
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
                                @foreach($stockEntryBatch->entries as $entry)
                                    <tr>
                                        <td>
                                            <strong>{{ $entry->product->name }}</strong><br>
                                            <small class="text-muted">{{ $entry->product->code }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $entry->product->category->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                +{{ $entry->quantity }} {{ $entry->product->unit->abbreviation }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $entry->product->current_stock }}</strong> {{ $entry->product->unit->abbreviation }}<br>
                                            <span class="badge bg-{{ $entry->product->stock_status_color }}">
                                                {{ $entry->product->stock_status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $entry->product) }}" 
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
                                    <td><strong class="text-success">+{{ $stockEntryBatch->total_quantity }} unidades</strong></td>
                                    <td colspan="2"><strong>{{ $stockEntryBatch->total_items }} productos</strong></td>
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