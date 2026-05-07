@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-down text-success"></i> Registrar Nueva Entrada</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-entries.index') }}">Entradas</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-plus-circle"></i> Datos de la Entrada
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock-entries.store') }}" id="entryForm">
                        @csrf

                        <!-- Producto -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">
                                Producto <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('product_id') is-invalid @enderror" 
                                    id="product_id" 
                                    name="product_id" 
                                    required>
                                <option value="">Seleccione un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        data-current-stock="{{ $product->current_stock }}"
                                        data-unit="{{ $product->unit->abbreviation }}"
                                        {{ old('product_id', $selectedProductId ?? '') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->code }}) - Stock: {{ $product->current_stock }} {{ $product->unit->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Info del producto seleccionado -->
                            <div id="productInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <strong>Stock Actual:</strong> <span id="currentStock">0</span> <span id="unit"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Proveedor -->
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">
                                Proveedor <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                    id="supplier_id" 
                                    name="supplier_id" 
                                    required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                        @if($supplier->ruc)
                                            - RUC: {{ $supplier->ruc }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <a href="{{ route('suppliers.create') }}" target="_blank">
                                    <i class="fas fa-plus"></i> ¿No encuentra el proveedor? Regístrelo aquí
                                </a>
                            </small>
                        </div>

                        <div class="row">
                            <!-- Cantidad -->
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">
                                    Cantidad <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ old('quantity', 1) }}"
                                       min="1"
                                       required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-6 mb-3">
                                <label for="entry_date" class="form-label">
                                    Fecha de Entrada <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('entry_date') is-invalid @enderror" 
                                       id="entry_date" 
                                       name="entry_date" 
                                       value="{{ old('entry_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('entry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tipo de Documento -->
                            <div class="col-md-6 mb-3">
                                <label for="document_type" class="form-label">Tipo de Documento</label>
                                <select class="form-select @error('document_type') is-invalid @enderror" 
                                        id="document_type" 
                                        name="document_type">
                                    <option value="">Seleccione un tipo</option>
                                    @foreach(\App\Models\StockEntry::getDocumentTypeLabels() as $key => $label)
                                        <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Número de Documento -->
                            <div class="col-md-6 mb-3">
                                <label for="document_number" class="form-label">Número de Documento</label>
                                <input type="text" 
                                       class="form-control @error('document_number') is-invalid @enderror" 
                                       id="document_number" 
                                       name="document_number" 
                                       value="{{ old('document_number') }}"
                                       placeholder="Ej: F001-00125">
                                @error('document_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas / Observaciones</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Información adicional sobre esta entrada...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Resumen -->
                        <div id="summary" class="alert alert-success" style="display: none;">
                            <h6><i class="fas fa-calculator"></i> Resumen de la Entrada</h6>
                            <p class="mb-1">
                                <strong>Stock Actual:</strong> <span id="summaryCurrentStock">0</span> <span id="summaryUnit"></span>
                            </p>
                            <p class="mb-1">
                                <strong>Cantidad a Ingresar:</strong> <span id="summaryQuantity">0</span> <span id="summaryUnit2"></span>
                            </p>
                            <p class="mb-0">
                                <strong>Nuevo Stock:</strong> 
                                <span class="text-success fw-bold" id="summaryNewStock">0</span> 
                                <span id="summaryUnit3"></span>
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock-entries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Registrar Entrada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const productInfo = document.getElementById('productInfo');
    const summary = document.getElementById('summary');

    function updateInfo() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        
        if (selectedOption.value) {
            const currentStock = parseInt(selectedOption.dataset.currentStock);
            const unit = selectedOption.dataset.unit;
            const quantity = parseInt(quantityInput.value) || 0;
            const newStock = currentStock + quantity;

            // Mostrar info del producto
            document.getElementById('currentStock').textContent = currentStock;
            document.getElementById('unit').textContent = unit;
            productInfo.style.display = 'block';

            // Mostrar resumen
            document.getElementById('summaryCurrentStock').textContent = currentStock;
            document.getElementById('summaryQuantity').textContent = quantity;
            document.getElementById('summaryNewStock').textContent = newStock;
            document.getElementById('summaryUnit').textContent = unit;
            document.getElementById('summaryUnit2').textContent = unit;
            document.getElementById('summaryUnit3').textContent = unit;
            summary.style.display = 'block';
        } else {
            productInfo.style.display = 'none';
            summary.style.display = 'none';
        }
    }

    productSelect.addEventListener('change', updateInfo);
    quantityInput.addEventListener('input', updateInfo);

    // Actualizar si ya hay un producto seleccionado
    if (productSelect.value) {
        updateInfo();
    }
});
</script>
@endpush
@endsection