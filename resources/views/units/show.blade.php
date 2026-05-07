@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="fas fa-ruler"></i> {{ $unit->name }}
                <span class="badge bg-primary fs-5 ms-2">{{ $unit->abbreviation }}</span>
            </h2>
            <p class="text-muted">Detalles de la unidad de medida</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('units.edit', $unit) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Información de la Unidad -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Productos</h6>
                    <h2 class="text-primary">{{ $unit->products_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Abreviatura</h6>
                    <h2>
                        <span class="badge bg-primary">{{ $unit->abbreviation }}</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Fecha Creación</h6>
                    <p class="mb-0">{{ $unit->created_at->format('d/m/Y') }}</p>
                    <small class="text-muted">{{ $unit->created_at->format('H:i') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Última Actualización</h6>
                    <p class="mb-0">{{ $unit->updated_at->format('d/m/Y') }}</p>
                    <small class="text-muted">{{ $unit->updated_at->format('H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos que usan esta Unidad -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-boxes"></i> Productos que usan esta Unidad de Medida
            </h5>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Estado Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td><strong>{{ $product->code }}</strong></td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $product->current_stock }}</strong> {{ $unit->abbreviation }}
                                    </td>
                                    <td>{{ $product->min_stock }} {{ $unit->abbreviation }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_status_color }}">
                                            {{ $product->stock_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver producto">
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
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay productos usando esta unidad de medida</h5>
                    <p class="text-muted">Cuando agregue productos con esta unidad, aparecerán aquí.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas Adicionales -->
    @if($products->count() > 0)
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Stock Normal</h6>
                        <h3>{{ $products->filter(fn($p) => $p->stock_status === 'normal')->count() }}</h3>
                        <small>productos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6>Stock Bajo</h6>
                        <h3>{{ $products->filter(fn($p) => $p->stock_status === 'low_stock')->count() }}</h3>
                        <small>productos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Agotados</h6>
                        <h3>{{ $products->filter(fn($p) => $p->stock_status === 'out_of_stock')->count() }}</h3>
                        <small>productos</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection