@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-user-plus"></i> Nuevo Usuario</h2>
            <p class="text-muted">Crear una nueva cuenta de usuario en el sistema</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <!-- Información Personal -->
                        <h6 class="border-bottom pb-2 mb-3">Datos Personales</h6>

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nombre Completo <span class="text-danger">*</span>
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
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Correo Electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Este email se usará para iniciar sesión</div>
                        </div>

                        <!-- Contraseña -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Credenciales de Acceso</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Mínimo 8 caracteres</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                    <div class="form-text">Debe coincidir con la contraseña</div>
                                </div>
                            </div>
                        </div>

                        <!-- Rol y Estado -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Permisos y Estado</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Rol -->
                                <div class="mb-3">
                                    <label for="role" class="form-label">
                                        Rol <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('role') is-invalid @enderror" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Seleccione un rol</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                            Administrador
                                        </option>
                                        <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>
                                            Operador
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <small id="roleHelp">Seleccione un rol para ver sus permisos</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                            Activo
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactivo
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Usuario
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
document.getElementById('role').addEventListener('change', function() {
    const roleHelp = document.getElementById('roleHelp');
    if (this.value === 'admin') {
        roleHelp.textContent = 'Acceso completo: Gestión de usuarios, productos, categorías y reportes';
        roleHelp.className = 'text-danger';
    } else if (this.value === 'operator') {
        roleHelp.textContent = 'Acceso limitado: Solo entradas, salidas y consultas';
        roleHelp.className = 'text-primary';
    } else {
        roleHelp.textContent = 'Seleccione un rol para ver sus permisos';
        roleHelp.className = '';
    }
});
</script>
@endpush
@endsection