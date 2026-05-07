@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-tag"></i> {{ $category->name }}</h2>
            <p class="text-muted">{{ $category->description ?? 'Sin descripción' }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Información de la Categoría -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Productos</h6>
                    <h2 class="text-primary">{{ $category->products_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Estado</h6>
                    <h4>
                        <span class="badge bg-{{ $category->status_color }}">
                            {{ $category->status_label }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Fecha Creación</h6>
                    <p class="mb-0">{{ $category->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Última Actualización</h6>
                    <p class="mb-0">{{ $category->updated_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos de esta Categoría -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-boxes"></i> Productos de esta Categoría
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
                                        <strong>{{ $product->current_stock }}</strong> {{ $product->unit->abbreviation }}
                                    </td>
                                    <td>{{ $product->min_stock }} {{ $product->unit->abbreviation }}</td>
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
                    <h5 class="text-muted">No hay productos en esta categoría</h5>
                    <p class="text-muted">Cuando agregue productos a esta categoría, aparecerán aquí.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection