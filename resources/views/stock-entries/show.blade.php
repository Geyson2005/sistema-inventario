@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-down text-success"></i> Detalles de la Entrada</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-entries.index') }}">Entradas</a></li>
                    <li class="breadcrumb-item active">Entrada #{{ $stockEntry->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información de la Entrada -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-info-circle"></i> Información de la Entrada
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Entrada:</strong></td>
                            <td>#{{ $stockEntry->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Entrada:</strong></td>
                            <td>
                                {{ $stockEntry->entry_date->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $stockEntry->entry_date->diffForHumans() }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Producto:</strong></td>
                            <td>
                                <strong>{{ $stockEntry->product->name }}</strong><br>
                                <small class="text-muted">
                                    Código: {{ $stockEntry->product->code }}<br>
                                    Categoría: {{ $stockEntry->product->category->name }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cantidad:</strong></td>
                            <td>
                                <span class="text-success fw-bold fs-4">
                                    +{{ $stockEntry->quantity }} {{ $stockEntry->product->unit->abbreviation }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Proveedor:</strong></td>
                            <td>
                                <a href="{{ route('suppliers.show', $stockEntry->supplier) }}" class="text-decoration-none">
                                    <span class="badge bg-secondary fs-6">
                                        {{ $stockEntry->supplier->name }}
                                    </span>
                                </a>
                                @if($stockEntry->supplier->ruc)
                                    <br><small class="text-muted">RUC: {{ $stockEntry->supplier->ruc }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Registrado por:</strong></td>
                            <td>
                                {{ $stockEntry->user->name }}<br>
                                <small class="text-muted">
                                    {{ $stockEntry->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                        </tr>
                        @if($stockEntry->updated_at != $stockEntry->created_at)
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>
                                    <small class="text-muted">
                                        {{ $stockEntry->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                            </tr>
                        @endif
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('stock-entries.edit', $stockEntry) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Entrada
                            </a>
                        @endif
                        <a href="{{ route('stock-entries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Documento -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-file-invoice"></i> Información del Documento
                </div>
                <div class="card-body">
                    @if($stockEntry->document_type)
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Tipo de Documento:</strong></td>
                                <td>
                                    <span class="badge bg-info fs-6">
                                        {{ $stockEntry->document_type_label }}
                                    </span>
                                </td>
                            </tr>
                            @if($stockEntry->document_number)
                                <tr>
                                    <td><strong>Número:</strong></td>
                                    <td>
                                        <span class="fw-bold">{{ $stockEntry->document_number }}</span>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Documento Completo:</strong></td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        {{ $stockEntry->full_document }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Esta entrada no tiene documento asociado.
                        </div>
                    @endif

                    @if($stockEntry->notes)
                        <hr>
                        <h6><i class="fas fa-sticky-note"></i> Notas</h6>
                        <div class="alert alert-light">
                            {{ $stockEntry->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stock del Producto Actual -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-boxes"></i> Stock Actual del Producto
                </div>
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">{{ $stockEntry->product->name }}</h6>
                    <h1 class="display-4 mb-0">{{ $stockEntry->product->current_stock }}</h1>
                    <p class="text-muted mb-3">{{ $stockEntry->product->unit->abbreviation }}</p>
                    
                    <span class="badge bg-{{ $stockEntry->product->stock_status_color }} fs-6">
                        {{ $stockEntry->product->stock_status_label }}
                    </span>

                    <hr class="my-3">

                    <div class="d-grid gap-2">
                        <a href="{{ route('products.show', $stockEntry->product) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Ver Producto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimiento de Stock Relacionado -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-exchange-alt"></i> Movimiento de Stock Relacionado
                </div>
                <div class="card-body">
                    @php
                        $movement = $stockEntry->product->stockMovements()
                            ->where('reference_id', $stockEntry->id)
                            ->where('reference_type', get_class($stockEntry))
                            ->first();
                    @endphp

                    @if($movement)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha del Movimiento</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Stock Anterior</th>
                                        <th>Stock Nuevo</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type_color }}">
                                                {{ $movement->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                +{{ $movement->quantity }} {{ $stockEntry->product->unit->abbreviation }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->previous_stock }} {{ $stockEntry->product->unit->abbreviation }}</td>
                                        <td>
                                            <strong>{{ $movement->new_stock }} {{ $stockEntry->product->unit->abbreviation }}</strong>
                                        </td>
                                        <td>{{ $movement->notes }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> 
                            No se encontró el movimiento de stock relacionado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection