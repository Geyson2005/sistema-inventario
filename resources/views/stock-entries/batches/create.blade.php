@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-down text-success"></i> Nueva Entrada Múltiple</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-entry-batches.index') }}">Entradas</a></li>
                    <li class="breadcrumb-item active">Nueva Múltiple</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h5><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('stock-entry-batches.store') }}" id="entryForm">
        @csrf

        <div class="row">
            <!-- Datos Generales -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-info-circle"></i> Datos Generales
                    </div>
                    <div class="card-body">
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
                        </div>

                        <!-- Fecha -->
                        <div class="mb-3">
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

                        <!-- Tipo de Documento -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="document_type" name="document_type">
                                <option value="">Seleccione un tipo</option>
                                @foreach(\App\Models\StockEntryBatch::getDocumentTypeLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Número de Documento -->
                        <div class="mb-3">
                            <label for="document_number" class="form-label">Número de Documento</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="document_number" 
                                   name="document_number" 
                                   value="{{ old('document_number') }}">
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas / Observaciones</label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Información adicional...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Productos -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-boxes"></i> Productos a Ingresar
                    </div>
                    <div class="card-body">
                        <!-- Selector de Producto -->
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label for="product_select" class="form-label">Seleccionar Producto</label>
                                <select class="form-select" id="product_select">
                                    <option value="">Busque y seleccione un producto...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-name="{{ $product->name }}"
                                            data-code="{{ $product->code }}"
                                            data-unit="{{ $product->unit->abbreviation }}"
                                            data-current-stock="{{ $product->current_stock }}"
                                            data-category="{{ $product->category->name }}">
                                            {{ $product->name }} ({{ $product->code }}) - Stock: {{ $product->current_stock }} {{ $product->unit->abbreviation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity_input" class="form-label">Cantidad</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantity_input" 
                                       min="1" 
                                       value="1"
                                       placeholder="Cantidad">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" id="addProductBtn">
                                    <i class="fas fa-plus"></i> Añadir
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de Productos Añadidos -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Producto</th>
                                        <th width="15%">Stock Actual</th>
                                        <th width="15%">Cantidad</th>
                                        <th width="15%">Nuevo Stock</th>
                                        <th width="15%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            No hay productos añadidos. Seleccione un producto y haga clic en "Añadir"
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen -->
                        <div class="alert alert-success mt-3" id="summary" style="display: none;">
                            <h6><i class="fas fa-calculator"></i> Resumen de la Entrada</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Total de Productos:</strong> <span id="totalProducts">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total de Unidades:</strong> <span id="totalQuantity">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Proveedor:</strong> <span id="summarySupplier">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('stock-entry-batches.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> Registrar Entrada Múltiple
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_select');
    const quantityInput = document.getElementById('quantity_input');
    const addProductBtn = document.getElementById('addProductBtn');
    const productsTableBody = document.getElementById('productsTableBody');
    const emptyRow = document.getElementById('emptyRow');
    const summary = document.getElementById('summary');
    const submitBtn = document.getElementById('submitBtn');
    const supplierSelect = document.getElementById('supplier_id');
    
    let products = [];
    let productIndex = 0;

    // Añadir producto
    addProductBtn.addEventListener('click', function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        
        if (!selectedOption.value) {
            alert('Por favor seleccione un producto');
            return;
        }

        const quantity = parseInt(quantityInput.value);
        if (quantity < 1) {
            alert('La cantidad debe ser mayor a 0');
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.dataset.name;
        const productCode = selectedOption.dataset.code;
        const unit = selectedOption.dataset.unit;
        const currentStock = parseInt(selectedOption.dataset.currentStock);
        const category = selectedOption.dataset.category;

        // Verificar si el producto ya está en la lista
        if (products.some(p => p.productId === productId)) {
            alert('Este producto ya está en la lista');
            return;
        }

        const newStock = currentStock + quantity;

        // Añadir a la lista
        products.push({
            index: productIndex,
            productId: productId,
            productName: productName,
            productCode: productCode,
            quantity: quantity,
            unit: unit,
            currentStock: currentStock,
            newStock: newStock,
            category: category
        });

        // Añadir fila a la tabla
        const row = document.createElement('tr');
        row.id = `product-row-${productIndex}`;
        row.innerHTML = `
            <td>
                <strong>${productName}</strong><br>
                <small class="text-muted">${productCode} | ${category}</small>
                <input type="hidden" name="products[${productIndex}][product_id]" value="${productId}">
                <input type="hidden" name="products[${productIndex}][quantity]" value="${quantity}">
            </td>
            <td>${currentStock} ${unit}</td>
            <td><strong class="text-success">+${quantity} ${unit}</strong></td>
            <td><strong>${newStock} ${unit}</strong></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${productIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        productsTableBody.appendChild(row);
        
        // Ocultar fila vacía
        emptyRow.style.display = 'none';

        // Limpiar selección
        productSelect.value = '';
        quantityInput.value = 1;

        // Actualizar resumen
        updateSummary();

        productIndex++;
    });

    // Función para remover producto (global)
    window.removeProduct = function(index) {
        products = products.filter(p => p.index !== index);
        document.getElementById(`product-row-${index}`).remove();
        
        if (products.length === 0) {
            emptyRow.style.display = '';
        }
        
        updateSummary();
    };

    // Actualizar resumen
    function updateSummary() {
        if (products.length === 0) {
            summary.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        const totalQuantity = products.reduce((sum, p) => sum + p.quantity, 0);
        const supplierName = supplierSelect.options[supplierSelect.selectedIndex].text;

        document.getElementById('totalProducts').textContent = products.length;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('summarySupplier').textContent = supplierName || '-';

        summary.style.display = 'block';
        submitBtn.disabled = false;
    }

    // Actualizar resumen al cambiar proveedor
    supplierSelect.addEventListener('change', updateSummary);
});
</script>
@endpush
@endsection