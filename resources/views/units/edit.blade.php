@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-edit"></i> Editar Unidad de Medida</h2>
            <p class="text-muted">Modificar información de: <strong>{{ $unit->name }} ({{ $unit->abbreviation }})</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos de la Unidad</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.update', $unit) }}" method="POST">
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
                                   value="{{ old('name', $unit->name) }}"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nombre completo de la unidad de medida</div>
                        </div>

                        <!-- Abreviatura -->
                        <div class="mb-3">
                            <label for="abbreviation" class="form-label">
                                Abreviatura <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('abbreviation') is-invalid @enderror" 
                                   id="abbreviation" 
                                   name="abbreviation" 
                                   value="{{ old('abbreviation', $unit->abbreviation) }}"
                                   maxlength="10"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Máximo 10 caracteres (se convertirá a mayúsculas)</div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Unidad
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
                    <h6>Productos que usan esta unidad:</h6>
                    <p class="h3 text-primary">{{ $unit->products()->count() }}</p>
                    
                    <hr>
                    
                    <h6>Fecha de Creación:</h6>
                    <p class="small">{{ $unit->created_at->format('d/m/Y H:i') }}</p>
                    
                    <h6>Última Actualización:</h6>
                    <p class="small">{{ $unit->updated_at->format('d/m/Y H:i') }}</p>

                    @if($unit->products()->count() > 0)
                        <div class="alert alert-info small mb-0 mt-3">
                            <i class="fas fa-info-circle"></i>
                            Esta unidad está siendo utilizada por <strong>{{ $unit->products()->count() }}</strong> producto(s). No podrá ser eliminada.
                        </div>
                    @else
                        <div class="alert alert-warning small mb-0 mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Esta unidad no tiene productos asociados y puede ser eliminada.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card de Advertencia si tiene productos -->
            @if($unit->products()->count() > 0)
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Advertencia</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-0">
                            Al modificar esta unidad, la información se actualizará en todos los productos que la utilizan. 
                            Asegúrese de que los cambios sean correctos.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection