@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-truck"></i> Detalles del Proveedor</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
                    <li class="breadcrumb-item active">{{ $supplier->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información del Proveedor -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle"></i> Información General
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $supplier->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>RUC:</strong></td>
                            <td>{{ $supplier->ruc ?? 'No registrado' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contacto:</strong></td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong></td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong></td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($supplier->status === 'active')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Registrado:</strong></td>
                            <td>{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Proveedor
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-8 mb-4">
            <div class="row">
                <!-- Total de Entradas -->
                <div class="col-md-4 mb-3">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Entradas</h6>
                            <h2 class="mb-0 text-success">{{ $totalEntries }}</h2>
                            <small class="text-muted">registros</small>
                        </div>
                    </div>
                </div>

                <!-- Cantidad Total Suministrada -->
                <div class="col-md-4 mb-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Cantidad Total</h6>
                            <h2 class="mb-0 text-primary">{{ $totalQuantity }}</h2>
                            <small class="text-muted">unidades</small>
                        </div>
                    </div>
                </div>

                <!-- Última Entrada -->
                <div class="col-md-4 mb-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Última Entrada</h6>
                            @if($supplier->stockEntries->count() > 0)
                                <h5 class="mb-0">
                                    {{ $supplier->stockEntries->first()->entry_date->format('d/m/Y') }}
                                </h5>
                                <small class="text-muted">
                                    {{ $supplier->stockEntries->first()->entry_date->diffForHumans() }}
                                </small>
                            @else
                                <p class="text-muted mb-0">Sin entradas</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos Más Comprados -->
            @if(isset($topProducts) && $topProducts->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Productos Más Comprados
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>
                                                <strong>{{ $item->total }}</strong> 
                                                {{ $item->product->unit->abbreviation }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Historial de Entradas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history"></i> Historial de Entradas desde este Proveedor
                </div>
                <div class="card-body">
                    @if($entries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Tipo Doc.</th>
                                        <th>N° Documento</th>
                                        <th>Usuario</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entries as $entry)
                                        <tr>
                                            <td>
                                                <small>{{ $entry->entry_date->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $entry->product->name }}</strong><br>
                                                <small class="text-muted">{{ $entry->product->code }}</small>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    +{{ $entry->quantity }} {{ $entry->product->unit->abbreviation }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($entry->document_type)
                                                    <span class="badge bg-info">
                                                        {{ $entry->document_type_label }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $entry->document_number ?? '-' }}</td>
                                            <td>
                                                <small>{{ $entry->user->name }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $entry->notes ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-3">
                            {{ $entries->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Este proveedor aún no tiene entradas registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection