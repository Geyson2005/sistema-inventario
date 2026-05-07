@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-user-plus"></i> Nuevo Cliente</h2>
            <p class="text-muted">Registrar un nuevo cliente en el sistema</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <!-- Información Básica -->
                        <h6 class="border-bottom pb-2 mb-3">Datos Generales</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <!-- Código -->
                                <div class="mb-3">
                                    <label for="code" class="form-label">
                                        Código <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code') }}"
                                  
                                           required
                                           autofocus>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nombre / Razón Social <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                         
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Documento de Identidad -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Documento de Identidad</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <!-- Tipo de Documento -->
                                <div class="mb-3">
                                    <label for="document_type" class="form-label">Tipo de Documento</label>
                                    <select class="form-select @error('document_type') is-invalid @enderror" 
                                            id="document_type" 
                                            name="document_type">
                                        <option value="">Sin documento</option>
                                        <option value="DNI" {{ old('document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                        <option value="RUC" {{ old('document_type') == 'RUC' ? 'selected' : '' }}>RUC</option>
                                        <option value="CE" {{ old('document_type') == 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                                        <option value="Pasaporte" {{ old('document_type') == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                        <option value="Otro" {{ old('document_type') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <!-- Número de Documento -->
                                <div class="mb-3">
                                    <label for="document_number" class="form-label">Número de Documento</label>
                                    <input type="text" 
                                           class="form-control @error('document_number') is-invalid @enderror" 
                                           id="document_number" 
                                           name="document_number" 
                                           value="{{ old('document_number') }}"
                                           >
                                    @error('document_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="documentHelp">DNI: 8 dígitos | RUC: 11 dígitos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Información de Contacto</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Teléfono -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                          >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      >{{ old('address') }}</textarea>
                            @error('address')
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
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Ayuda -->
        
    </div>
</div>

@push('scripts')
<script>
document.getElementById('document_type').addEventListener('change', function() {
    const helpText = document.getElementById('documentHelp');
    const input = document.getElementById('document_number');
    
    switch(this.value) {
        case 'DNI':
            helpText.textContent = 'DNI: 8 dígitos';
            input.placeholder = '12345678';
            input.maxLength = 8;
            break;
        case 'RUC':
            helpText.textContent = 'RUC: 11 dígitos';
            input.placeholder = '20123456789';
            input.maxLength = 11;
            break;
        case 'CE':
            helpText.textContent = 'Carnet de Extranjería';
            input.placeholder = 'Número de CE';
            input.maxLength = 20;
            break;
        case 'Pasaporte':
            helpText.textContent = 'Número de Pasaporte';
            input.placeholder = 'Número de pasaporte';
            input.maxLength = 20;
            break;
        default:
            helpText.textContent = '';
            input.placeholder = '';
            input.maxLength = 20;
    }
});
</script>
@endpush
@endsection