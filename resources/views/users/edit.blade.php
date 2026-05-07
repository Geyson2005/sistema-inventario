@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-user-edit"></i> Editar Usuario</h2>
            <p class="text-muted">Modificar información de: <strong>{{ $user->name }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                   value="{{ old('name', $user->name) }}"
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
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Cambiar Contraseña (Opcional)</h6>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Deja estos campos vacíos si no deseas cambiar la contraseña
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Mínimo 8 caracteres</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Confirmar Nueva Contraseña
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
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
                                            required
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            Administrador
                                        </option>
                                        <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>
                                            Operador
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($user->id === auth()->id())
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No puedes cambiar tu propio rol
                                        </div>
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                    @endif
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
                                            required
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>
                                            Activo
                                        </option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                            Inactivo
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($user->id === auth()->id())
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No puedes cambiar tu propio estado
                                        </div>
                                        <input type="hidden" name="status" value="{{ $user->status }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Usuario
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
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Actividad del Usuario</h5>
                </div>
                <div class="card-body">
                    @php
                        $activity = $user->getActivitySummary();
                    @endphp
                    
                    <h6>Entradas Registradas:</h6>
                    <p class="h3 text-success">{{ $activity['entries'] }}</p>
                    
                    <hr>
                    
                    <h6>Salidas Registradas:</h6>
                    <p class="h3 text-danger">{{ $activity['exits'] }}</p>
                    
                    <hr>
                    
                    <h6>Ajustes Realizados:</h6>
                    <p class="h3 text-warning">{{ $activity['adjustments'] }}</p>
                    
                    <hr>
                    
                    <h6>Total Movimientos:</h6>
                    <p class="h3 text-primary">{{ $activity['total_movements'] }}</p>
                </div>
            </div>

            <div class="card bg-light mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar"></i> Información de Registro</h6>
                </div>
                <div class="card-body">
                    <h6>Fecha de Creación:</h6>
                    <p class="small">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    
                    <h6>Última Actualización:</h6>
                    <p class="small">{{ $user->updated_at->format('d/m/Y H:i') }}</p>

                    @if($user->stockMovements()->count() > 0)
                        <div class="alert alert-info small mb-0 mt-3">
                            <i class="fas fa-shield-alt"></i>
                            Este usuario tiene {{ $user->stockMovements()->count() }} movimientos registrados. No podrá ser eliminado.
                        </div>
                    @else
                        <div class="alert alert-warning small mb-0 mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Este usuario no tiene movimientos y puede ser eliminado.
                        </div>
                    @endif
                </div>
            </div>

            @if($user->id === auth()->id())
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Nota:</strong> Estás editando tu propia cuenta. No puedes cambiar tu rol ni estado.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection