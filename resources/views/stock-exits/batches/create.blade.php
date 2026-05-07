@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-arrow-up text-danger"></i> Nueva Salida Múltiple</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-exit-batches.index') }}">Salidas</a></li>
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

    <form method="POST" action="{{ route('stock-exit-batches.store') }}" id="exitForm">
        @csrf

        <div class="row">
            <!-- Datos Generales -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-info-circle"></i> Datos Generales
                    </div>
                    <div class="card-body">
                        <!-- Fecha -->
                        <div class="mb-3">
                            <label for="exit_date" class="form-label">
                                Fecha de Salida <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                    class="form-control @error('exit_date') is-invalid @enderror" 
                                    id="exit_date" 
                                    name="exit_date" 
                                    value="{{ old('exit_date', date('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}"
                                    required>
                            @error('exit_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="client_id" class="form-label">
                                Cliente <span class="text-danger" id="client-required">*</span>
                            </label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" 
                                    name="client_id">
                                <option value="">Seleccione un cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->code }} - {{ $client->name }}
                                        @if($client->document_number)
                                            ({{ $client->document_type }}: {{ $client->document_number }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="client-help">Requerido para ventas</div>
                        </div>

                        <!-- Motivo -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">
                                Motivo de Salida <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('reason') is-invalid @enderror" 
                                    id="reason" 
                                    name="reason" 
                                    required>
                                <option value="">Seleccione un motivo</option>
                                @foreach(\App\Models\StockExitBatch::getReasonLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('reason') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo de Documento -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="document_type" name="document_type">
                                <option value="">Seleccione un tipo</option>
                                @foreach(\App\Models\StockExitBatch::getDocumentTypeLabels() as $key => $label)
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
                                   value="{{ old('document_number') }}"
                                   placeholder="Ej: B001-00125">
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
                        <i class="fas fa-boxes"></i> Productos a Retirar
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
                                            data-category="{{ optional($product->category)->name ?? '-' }}">
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
                        <div class="alert alert-danger mt-3" id="summary" style="display: none;">
                            <h6><i class="fas fa-calculator"></i> Resumen de la Salida</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Total de Productos:</strong> <span id="totalProducts">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total de Unidades:</strong> <span id="totalQuantity">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Motivo:</strong> <span id="summaryReason">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('stock-exit-batches.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> Registrar Salida Múltiple
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
    const reasonSelect = document.getElementById('reason');

    let products = [];
    let productIndex = 0;

    // Añadir producto
    addProductBtn.addEventListener('click', function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            alert('Por favor seleccione un producto');
            return;
        }

        const quantity = Number(quantityInput.value) || 0;
        if (quantity < 1) {
            alert('La cantidad debe ser mayor a 0');
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.dataset.name || '';
        const productCode = selectedOption.dataset.code || '';
        const unit = selectedOption.dataset.unit || '';
        const currentStock = Number(selectedOption.dataset.currentStock) || 0;
        const category = selectedOption.dataset.category || '-';

        // Verificar si el producto ya está en la lista
        if (products.some(p => p.productId === productId)) {
            alert('Este producto ya está en la lista');
            return;
        }

        // VALIDACIÓN: Verificar stock disponible
        if (currentStock < quantity) {
            alert(`Stock insuficiente para ${productName}.\nDisponible: ${currentStock} ${unit}\nSolicitado: ${quantity} ${unit}`);
            return;
        }

        const newStock = currentStock - quantity;

        // Añadir a la lista
        const currentIndex = productIndex;
        products.push({
            index: currentIndex,
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
        row.id = `product-row-${currentIndex}`;
        row.innerHTML = `
            <td>
                <strong>${escapeHtml(productName)}</strong><br>
                <small class="text-muted">${escapeHtml(productCode)} | ${escapeHtml(category)}</small>
                <input type="hidden" name="products[${currentIndex}][product_id]" value="${productId}">
                <input type="hidden" name="products[${currentIndex}][quantity]" value="${quantity}">
            </td>
            <td>${currentStock} ${escapeHtml(unit)}</td>
            <td><strong class="text-danger">-${quantity} ${escapeHtml(unit)}</strong></td>
            <td>
                <strong>${newStock} ${escapeHtml(unit)}</strong>
                ${newStock === 0 ? '<br><span class="badge bg-danger">AGOTADO</span>' : ''}
                ${newStock > 0 && newStock <= 10 ? '<br><span class="badge bg-warning text-dark">BAJO</span>' : ''}
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" data-index="${currentIndex}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        // Delegated event for delete button
        row.querySelector('button[data-index]').addEventListener('click', function() {
            removeProduct(currentIndex);
        });

        productsTableBody.appendChild(row);

        // Ocultar fila vacía
        if (emptyRow) emptyRow.style.display = 'none';

        // Limpiar selección
        productSelect.value = '';
        quantityInput.value = 1;

        // Actualizar resumen
        updateSummary();

        productIndex++;
    });

    // Función para remover producto
    function removeProduct(index) {
        products = products.filter(p => p.index !== index);
        const row = document.getElementById(`product-row-${index}`);
        if (row) row.remove();

        if (products.length === 0 && emptyRow) {
            emptyRow.style.display = '';
        }

        updateSummary();
    }

    // Actualizar resumen
    function updateSummary() {
        if (!Array.isArray(products) || products.length === 0) {
            summary.style.display = 'none';
            submitBtn.disabled = true;
            document.getElementById('totalProducts').textContent = 0;
            document.getElementById('totalQuantity').textContent = 0;
            document.getElementById('summaryReason').textContent = '-';
            return;
        }

        const totalQuantity = products.reduce((sum, p) => sum + Number(p.quantity), 0);
        const reasonName = reasonSelect && reasonSelect.selectedIndex > 0
            ? reasonSelect.options[reasonSelect.selectedIndex].text
            : '-';

        document.getElementById('totalProducts').textContent = products.length;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('summaryReason').textContent = reasonName || '-';

        summary.style.display = 'block';
        submitBtn.disabled = false;
    }

    // Actualizar resumen al cambiar motivo
    if (reasonSelect) {
        reasonSelect.addEventListener('change', updateSummary);
    }

    // Small helper to avoid XSS via text inserted into innerHTML (only simple escaping)
    function escapeHtml(text) {
        if (!text && text !== 0) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
</script>
@endpush
@push('scripts')
    <script>
    // Hacer cliente obligatorio si es venta
    document.getElementById('reason').addEventListener('change', function() {
        const clientSelect = document.getElementById('client_id');
        const clientRequired = document.getElementById('client-required');
        const clientHelp = document.getElementById('client-help');
        
        if (this.value === 'sale') {
            clientSelect.required = true;
            clientRequired.style.display = 'inline';
            clientHelp.textContent = 'Requerido para ventas';
            clientHelp.className = 'form-text text-danger';
        } else {
            clientSelect.required = false;
            clientRequired.style.display = 'none';
            clientHelp.textContent = 'Opcional para transferencias, daños u otros';
            clientHelp.className = 'form-text text-muted';
        }
    });

    // Ejecutar al cargar si hay un motivo seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        const reason = document.getElementById('reason').value;
        if (reason === 'sale') {
            document.getElementById('client_id').required = true;
        }
    });
</script>
@endpush
@endsection
