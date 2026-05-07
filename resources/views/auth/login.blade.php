<x-guest-layout>
    <div class="auth-header">
        <i class="fas fa-warehouse fa-3x mb-3"></i>
        <h3 class="mb-0">REFFSAC</h3>
        <p class="mb-0">Iniciar Sesión</p>
    </div>

    <div class="auth-body">
        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username">
                </div>
                @error('email')
                    <div class="text-danger mt-1">
                        <small>{{ $message }}</small>
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password">
                </div>
                @error('password')
                    <div class="text-danger mt-1">
                        <small>{{ $message }}</small>
                    </div>
                @enderror
            </div>

            

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </form>

    </div>
</x-guest-layout>