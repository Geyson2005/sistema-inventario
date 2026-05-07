@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-arrow-up text-danger"></i> Salidas Múltiples</h2>
            <p class="text-muted">Registro de lotes de salida del almacén</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('stock-exit-batches.create') }}" class="btn btn-danger">
                <i class="fas fa-plus"></i> Nueva Salida Múltiple
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('stock-exit-batches.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar N° Documento</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Número de documento..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Motivo</label>
                    <select name="reason" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\StockExitBatch::getReasonLabels() as $key => $label)
                            <option value="{{ $key }}" 
                                {{ request('reason') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipo Documento</label>
                    <select name="document_type" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\StockExitBatch::getDocumentTypeLabels() as $key => $label)
                            <option value="{{ $key }}" 
                                {{ request('document_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Documento</th>
                            <th>Productos</th>
                            <th>Cantidad Total</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>
                                    <strong>{{ $batch->exit_date->format('d/m/Y') }}</strong><br>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $batch->reason_label }}</span>
                                </td>
                                <td>
                                    @if($batch->document_type)
                                        <span class="badge bg-info">{{ $batch->document_type_label }}</span><br>
                                        <small>{{ $batch->document_number }}</small>
                                    @else
                                        <span class="text-muted">Sin documento</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $batch->total_items }} productos</span>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold">
                                        -{{ $batch->total_quantity }} unidades
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $batch->user->name }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('stock-exit-batches.show', $batch) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(Auth::user()->isAdmin())
                                            <form action="{{ route('stock-exit-batches.destroy', $batch) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('⚠️ ADVERTENCIA: Eliminar este lote revertirá el stock de TODOS los productos incluidos.\n¿Está seguro de continuar?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron salidas múltiples</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection