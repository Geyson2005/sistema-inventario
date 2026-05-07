@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-plus-circle"></i> Nueva Unidad de Medida</h2>
            <p class="text-muted">Crear una nueva unidad de medida para productos</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos de la Unidad</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
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
                                   value="{{ old('abbreviation') }}"
                                   maxlength="10"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Unidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection