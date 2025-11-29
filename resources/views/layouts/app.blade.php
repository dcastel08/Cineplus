<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .movie-card { transition: transform 0.3s; }
        .movie-card:hover { transform: translateY(-5px); }
        .seat { width: 30px; height: 30px; margin: 2px; cursor: pointer; }
        .seat.available { background-color: #28a745; }
        .seat.occupied { background-color: #dc3545; cursor: not-allowed; }
        .seat.selected { background-color: #007bff; }
        .seat.vip { border: 2px solid gold; }
        .seat.disabled { background-color: #6c757d; cursor: not-allowed; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-film"></i> CinePlus
            </a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                    @elseif(Auth::user()->isCashier())
                        <a class="nav-link" href="{{ route('cashier.dashboard') }}">Cajero</a>
                    @endif
                    <a class="nav-link" href="{{ route('bookings.my-bookings') }}">Mis Reservas</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Cerrar Sesión</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                    <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 CinePlus. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>