@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-edit"></i> Editar Entrada</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-entries.index') }}">Entradas</a></li>
                    <li class="breadcrumb-item active">Editar #{{ $stockEntry->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <i class="fas fa-exclamation-triangle"></i> Editar Datos de la Entrada
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Nota Importante:</strong> Por razones de integridad del inventario, 
                        NO se puede modificar el producto ni la cantidad. Solo puede editar los datos 
                        del proveedor, fecha y documento.
                    </div>

                    <form method="POST" action="{{ route('stock-entries.update', $stockEntry) }}">
                        @csrf
                        @method('PUT')

                        <!-- Producto (Solo lectura) -->
                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $stockEntry->product->name }} ({{ $stockEntry->product->code }})"
                                   readonly>
                            <small class="form-text text-muted">Este campo no se puede modificar</small>
                        </div>

                        <!-- Cantidad (Solo lectura) -->
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $stockEntry->quantity }} {{ $stockEntry->product->unit->abbreviation }}"
                                   readonly>
                            <small class="form-text text-muted">Este campo no se puede modificar</small>
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
                                        {{ old('supplier_id', $stockEntry->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                                   value="{{ old('entry_date', $stockEntry->entry_date->format('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('entry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                        <option value="{{ $key }}" 
                                            {{ old('document_type', $stockEntry->document_type) == $key ? 'selected' : '' }}>
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
                                       value="{{ old('document_number', $stockEntry->document_number) }}"
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
                                      placeholder="Información adicional sobre esta entrada...">{{ old('notes', $stockEntry->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info adicional -->
                        <div class="alert alert-info">
                            <strong>Registrado por:</strong> {{ $stockEntry->user->name }}<br>
                            <strong>Fecha de registro:</strong> {{ $stockEntry->created_at->format('d/m/Y H:i') }}
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock-entries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Entrada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection