@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-chart-bar"></i> Centro de Reportes</h2>
            <p class="text-muted">Genera reportes personalizados del sistema de inventario</p>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Total Productos</h6>
                    <h2 class="text-primary">{{ $stats['total_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h6 class="text-muted">Stock Bajo</h6>
                    <h2 class="text-warning">{{ $stats['low_stock_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h6 class="text-muted">Agotados</h6>
                    <h2 class="text-danger">{{ $stats['out_of_stock_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Proveedores</h6>
                    <h2 class="text-success">{{ $stats['total_suppliers'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos del Mes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Movimientos del Mes Actual</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <i class="fas fa-arrow-down fa-2x text-success mb-2"></i>
                            <h3 class="text-success">{{ $monthlyStats['entries'] }}</h3>
                            <p class="text-muted">Entradas</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-arrow-up fa-2x text-danger mb-2"></i>
                            <h3 class="text-danger">{{ $monthlyStats['exits'] }}</h3>
                            <p class="text-muted">Salidas</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-adjust fa-2x text-warning mb-2"></i>
                            <h3 class="text-warning">{{ $monthlyStats['adjustments'] }}</h3>
                            <p class="text-muted">Ajustes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Generación de Reportes -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-alt"></i> Generar Reporte Personalizado</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.generate') }}" method="POST" id="reportForm">
                @csrf

                <!-- Tipo de Reporte -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">
                            <i class="fas fa-file-chart"></i> Tipo de Reporte <span class="text-danger">*</span>
                        </label>
                        <select name="report_type" id="report_type" class="form-select form-select-lg" required>
                            <option value="">Seleccione un tipo de reporte</option>
                            <option value="inventory">📦 Inventario Actual</option>
                            <option value="movements">🔄 Movimientos de Stock</option>
                            <option value="suppliers">🚚 Reporte de Proveedores</option>
                            <option value="clients">👥 Reporte de Clientes</option>
                            <option value="low_stock">⚠️ Productos con Stock Bajo</option>
                            <option value="categories">📑 Reporte por Categorías</option>
                            <option value="users_activity">👥 Actividad de Usuarios</option>
                        </select>
                    </div>
                </div>

                <!-- Filtros (se muestran según tipo de reporte) -->
                <div id="filters-container">
                    <!-- Filtros de Fecha -->
                    <div class="row mb-3 filter-group" id="date-filters" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Filtro por Categoría -->
                    <div class="row mb-3 filter-group" id="category-filter" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Categoría</label>
                            <select name="category_id" class="form-select">
                                <option value="">Todas las categorías</option>
                                @foreach(\App\Models\Category::active()->orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filtro por Proveedor -->
                    <div class="row mb-3 filter-group" id="supplier-filter" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Todos los proveedores</option>
                                @foreach(\App\Models\Supplier::active()->orderBy('name')->get() as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 filter-group" id="client-filter" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Cliente</label>
                        <select name="client_id" class="form-select">
                            <option value="">Todos los clientes</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->code }} - {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <!-- Filtro por Tipo de Movimiento -->
                    <div class="row mb-3 filter-group" id="movement-filter" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Movimiento</label>
                            <select name="movement_type" class="form-select">
                                <option value="">Todos los tipos</option>
                                <option value="entry">Entradas</option>
                                <option value="exit">Salidas</option>
                                <option value="adjustment">Ajustes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Formato de Salida -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">
                            <i class="fas fa-file-export"></i> Formato de Salida <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="format" id="format-view" value="view" checked>
                            <label class="btn btn-outline-primary" for="format-view">
                                <i class="fas fa-eye"></i> Ver en Pantalla
                            </label>

                            <input type="radio" class="btn-check" name="format" id="format-pdf" value="pdf">
                            <label class="btn btn-outline-danger" for="format-pdf">
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </label>

                            <input type="radio" class="btn-check" name="format" id="format-excel" value="excel">
                            <label class="btn btn-outline-success" for="format-excel">
                                <i class="fas fa-file-excel"></i> Descargar Excel
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botón de Generar -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-chart-line"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Descripción de Reportes -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Descripción de Reportes Disponibles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-box"></i> Inventario Actual</h6>
                            <p class="small">Lista completa de todos los productos con su stock actual, categoría y estado.</p>

                            <h6 class="text-primary"><i class="fas fa-exchange-alt"></i> Movimientos de Stock</h6>
                            <p class="small">Historial detallado de entradas, salidas y ajustes de inventario por fechas.</p>

                            <h6 class="text-primary"><i class="fas fa-truck"></i> Reporte de Proveedores</h6>
                            <p class="small">Estadísticas de proveedores con cantidad de entradas y productos suministrados.</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</h6>
                            <p class="small">Productos que están por debajo del stock mínimo o agotados.</p>

                            <h6 class="text-primary"><i class="fas fa-tags"></i> Reporte por Categorías</h6>
                            <p class="small">Análisis de inventario agrupado por categorías de productos.</p>

                            <h6 class="text-primary"><i class="fas fa-users"></i> Actividad de Usuarios</h6>
                            <p class="small">Registro de actividades realizadas por cada usuario del sistema.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('report_type').addEventListener('change', function() {
    const reportType = this.value;
    
    // Ocultar todos los filtros
    document.querySelectorAll('.filter-group').forEach(el => el.style.display = 'none');
    
    // Mostrar filtros según tipo de reporte
    switch(reportType) {
        case 'movements':
        case 'users_activity':
            document.getElementById('date-filters').style.display = 'flex';
            if (reportType === 'movements') {
                document.getElementById('movement-filter').style.display = 'flex';
            }
            break;
        case 'inventory':
        case 'low_stock':
            document.getElementById('category-filter').style.display = 'flex';
            break;
        case 'suppliers':
            document.getElementById('date-filters').style.display = 'flex';
            document.getElementById('supplier-filter').style.display = 'flex';
            break;
        case 'clients':
            document.getElementById('date-filters').style.display = 'flex';
            document.getElementById('client-filter').style.display = 'flex';
            break;
    }
});
</script>
@endpush
@endsection