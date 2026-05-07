@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="fas fa-user"></i> {{ $client->name }}
                @if($client->document_number)
                    <span class="badge bg-dark fs-6 ms-2">{{ $client->document_type }}: {{ $client->formatted_document }}</span>
                @endif
            </h2>
            <p class="text-muted mb-1">
                <i class="fas fa-barcode"></i> Código: <strong>{{ $client->code }}</strong>
            </p>
            @if($client->phone || $client->email)
                <p class="text-muted small mb-0">
                    @if($client->phone)
                        <i class="fas fa-phone"></i> {{ $client->phone }}
                    @endif
                    @if($client->email)
                        <span class="ms-3"><i class="fas fa-envelope"></i> {{ $client->email }}</span>
                    @endif
                </p>
            @endif
            @if($client->address)
                <p class="text-muted small">
                    <i class="fas fa-map-marker-alt"></i> {{ $client->address }}
                </p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Total Compras</h6>
                    <h2 class="text-primary">{{ $stats['total_purchases'] }}</h2>
                    <small class="text-muted">Lotes de salida</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="fas fa-boxes fa-2x text-info mb-2"></i>
                    <h6 class="text-muted">Productos Diferentes</h6>
                    <h2 class="text-info">{{ $stats['total_exits'] }}</h2>
                    <small class="text-muted">Productos comprados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="fas fa-cubes fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Cantidad Total</h6>
                    <h2 class="text-success">{{ number_format($stats['total_quantity']) }}</h2>
                    <small class="text-muted">Unidades compradas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Estado</h6>
                    <h3>
                        <span class="badge bg-{{ $client->status_color }}">
                            {{ $client->status_label }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Compras -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-history"></i> Historial de Compras
            </h5>
        </div>
        <div class="card-body">
            @if($recentPurchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo Doc.</th>
                                <th>N° Documento</th>
                                <th>Motivo</th>
                                <th>Productos</th>
                                <th>Cantidad Total</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPurchases as $batch)
                                <tr>
                                    <td>{{ $batch->exit_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $batch->document_type ?? 'N/A')) }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $batch->document_number ?? '-' }}</strong></td>
                                    <td>
                                        @php
                                            $reasonLabels = [
                                                'sale' => 'Venta',
                                                'transfer' => 'Transferencia',
                                                'damage' => 'Daño',
                                                'other' => 'Otro'
                                            ];
                                            $reasonColors = [
                                                'sale' => 'success',
                                                'transfer' => 'info',
                                                'damage' => 'danger',
                                                'other' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $reasonColors[$batch->reason] ?? 'secondary' }}">
                                            {{ $reasonLabels[$batch->reason] ?? 'Desconocido' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $batch->total_items }} items</span>
                                    </td>
                                    <td><strong>{{ number_format($batch->total_quantity) }}</strong> unidades</td>
                                    <td class="small">{{ $batch->user->name }}</td>
                                    <td>
                                        <a href="{{ route('stock-exit-batches.show', $batch) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $recentPurchases->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay compras registradas</h5>
                    <p class="text-muted">Este cliente aún no tiene compras en el sistema.</p>
                    <a href="{{ route('stock-exit-batches.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Registrar Salida
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">Código:</th>
                            <td><strong>{{ $client->code }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nombre Completo:</th>
                            <td>{{ $client->name }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Documento:</th>
                            <td>{{ $client->document_type_label }}</td>
                        </tr>
                        <tr>
                            <th>N° Documento:</th>
                            <td>{{ $client->formatted_document }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $client->phone ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $client->email ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Dirección:</th>
                            <td>{{ $client->address ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $client->status_color }}">
                                    {{ $client->status_label }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar"></i> Fechas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">Fecha de Registro:</th>
                            <td>{{ $client->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $client->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @php
                            $firstPurchase = $client->stockExitBatches()->oldest('exit_date')->first();
                            $lastPurchase = $client->stockExitBatches()->latest('exit_date')->first();
                        @endphp
                        @if($firstPurchase)
                            <tr>
                                <th>Primera Compra:</th>
                                <td>{{ $firstPurchase->exit_date->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                        @if($lastPurchase)
                            <tr>
                                <th>Última Compra:</th>
                                <td>{{ $lastPurchase->exit_date->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($client->stockExitBatches()->count() > 0)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Protegido:</strong> Este cliente no puede ser eliminado porque tiene compras registradas.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection