@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="bg-{{ $user->role_color }} text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">
                    {{ $user->initials }}
                </div>
                <div>
                    <h2 class="mb-1">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="badge bg-info fs-6">Tú</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Información General -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Rol</h6>
                    <h4>
                        <span class="badge bg-{{ $user->role_color }}">
                            {{ $user->role_label }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Estado</h6>
                    <h4>
                        <span class="badge bg-{{ $user->status_color }}">
                            {{ $user->status_label }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Miembro desde</h6>
                    <p class="mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Movimientos</h6>
                    <h2 class="text-primary">{{ $user->stock_movements_count }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Actividad -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                    <h6>Entradas de Stock</h6>
                    <h2>{{ $user->stock_entries_count }}</h2>
                    <small>Registros de entrada</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                    <h6>Salidas de Stock</h6>
                    <h2>{{ $user->stock_exits_count }}</h2>
                    <small>Registros de salida</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-adjust fa-2x mb-2"></i>
                    <h6>Ajustes de Inventario</h6>
                    <h2>{{ $user->stock_adjustments_count }}</h2>
                    <small>Ajustes realizados</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#entries">
                                <i class="fas fa-arrow-down text-success"></i> Entradas Recientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#exits">
                                <i class="fas fa-arrow-up text-danger"></i> Salidas Recientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#adjustments">
                                <i class="fas fa-adjust text-warning"></i> Ajustes Recientes
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Entradas Recientes -->
                        <div class="tab-pane fade show active" id="entries">
                            @if($recentActivity['entries']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Documento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentActivity['entries'] as $entry)
                                                <tr>
                                                    <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                                                    <td>{{ $entry->product->name }}</td>
                                                    <td><span class="badge bg-success">+{{ $entry->quantity }}</span></td>
                                                    <td class="small">{{ $entry->document_number ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted py-3">No hay entradas recientes</p>
                            @endif
                        </div>

                        <!-- Salidas Recientes -->
                        <div class="tab-pane fade" id="exits">
                            @if($recentActivity['exits']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Motivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentActivity['exits'] as $exit)
                                                <tr>
                                                    <td>{{ $exit->exit_date->format('d/m/Y') }}</td>
                                                    <td>{{ $exit->product->name }}</td>
                                                    <td><span class="badge bg-danger">-{{ $exit->quantity }}</span></td>
                                                    <td class="small">{{ ucfirst($exit->reason) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted py-3">No hay salidas recientes</p>
                            @endif
                        </div>

                        <!-- Ajustes Recientes -->
                        <div class="tab-pane fade" id="adjustments">
                            @if($recentActivity['adjustments']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Tipo</th>
                                                <th>Razón</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentActivity['adjustments'] as $adjustment)
                                                <tr>
                                                    <td>{{ $adjustment->adjustment_date->format('d/m/Y') }}</td>
                                                    <td>{{ $adjustment->product->name }}</td>
                                                    <td>
                                                        @if($adjustment->type === 'increase')
                                                            <span class="badge bg-success">+{{ $adjustment->quantity }}</span>
                                                        @else
                                                            <span class="badge bg-danger">-{{ $adjustment->quantity }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $adjustment->type === 'increase' ? 'success' : 'warning' }}">
                                                            {{ $adjustment->type === 'increase' ? 'Aumento' : 'Disminución' }}
                                                        </span>
                                                    </td>
                                                    <td class="small">{{ $adjustment->reason }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted py-3">No hay ajustes recientes</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Detalles de la Cuenta</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">Nombre Completo:</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Rol:</th>
                            <td>
                                <span class="badge bg-{{ $user->role_color }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $user->status_color }}">
                                    {{ $user->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Permisos del Rol</h6>
                </div>
                <div class="card-body">
                    @if($user->is_admin)
                        <h6 class="text-danger">Administrador - Acceso Completo</h6>
                        <ul class="small">
                            <li>Gestión de usuarios</li>
                            <li>Gestión de productos, categorías y unidades</li>
                            <li>Gestión de proveedores</li>
                            <li>Entradas y salidas de stock</li>
                            <li>Ajustes de inventario</li>
                            <li>Generación de reportes</li>
                        </ul>
                    @else
                        <h6 class="text-primary">Operador - Acceso Limitado</h6>
                        <ul class="small">
                            <li>Visualización de productos</li>
                            <li>Registro de entradas de stock</li>
                            <li>Registro de salidas de stock</li>
                            <li>Consulta de historial</li>
                            <li>Consulta de reportes básicos</li>
                        </ul>
                    @endif
                </div>
            </div>

            @if($user->stockMovements()->count() > 0)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-shield-alt"></i>
                    Este usuario tiene movimientos registrados y está protegido contra eliminación.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection