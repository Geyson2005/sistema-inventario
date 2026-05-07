<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-warehouse"></i> {{ config('app.name', 'Sistema de Almacén') }}
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i> Productos
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock-entry-batches.*') ? 'active' : '' }}" 
                        href="{{ route('stock-entry-batches.index') }}">
                            <i class="fas fa-arrow-down text-success"></i> Entradas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock-exit-batches.*') ? 'active' : '' }}" 
                        href="{{ route('stock-exit-batches.index') }}">
                            <i class="fas fa-arrow-up text-danger"></i> Salidas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </li>
                    
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags"></i> Categorías
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('units.index') }}">
                                    <i class="fas fa-ruler"></i> Unidades
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('suppliers.index') }}">
                                    <i class="fas fa-truck"></i> Proveedores
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('clients.index') }}">
                                    <i class="fas fa-truck"></i> Clientes
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> Usuarios
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('reports.index') }}">
                                    <i class="fas fa-chart-bar"></i> Reportes
                                </a></li>
                            </ul>
                        </li>
                    @endif
                @endauth
            </ul>
            
            @auth
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            <span class="badge bg-{{ Auth::user()->role === 'admin' ? 'danger' : 'info' }}">
                                {{ Auth::user()->role === 'admin' ? 'Admin' : 'Operador' }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-edit"></i> Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>