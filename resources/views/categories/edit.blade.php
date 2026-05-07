@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-edit"></i> Editar Categoría</h2>
            <p class="text-muted">Modificar información de la categoría: <strong>{{ $category->name }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos de la Categoría</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información</h5>
                </div>
                <div class="card-body">
                    <h6>Productos Asociados:</h6>
                    <p class="h3 text-primary">{{ $category->products()->count() }}</p>
                    
                    <hr>
                    
                    <h6>Fecha de Creación:</h6>
                    <p class="small">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    
                    <h6>Última Actualización:</h6>
                    <p class="small">{{ $category->updated_at->format('d/m/Y H:i') }}</p>

                    @if($category->products()->count() > 0)
                        <div class="alert alert-info small mb-0 mt-3">
                            <i class="fas fa-info-circle"></i>
                            Esta categoría tiene productos asociados. No podrá ser eliminada.
                        </div>
                    @else
                        <div class="alert alert-warning small mb-0 mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Esta categoría no tiene productos asociados y puede ser eliminada.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection