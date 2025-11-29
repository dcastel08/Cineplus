@extends('layouts.app')

@section('title', 'Dashboard Administrador - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Dashboard Administrador</h1>
            <p class="lead">Bienvenido, {{ $user->name }}</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Películas Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Movie::where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-film fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Reservas Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Booking::whereDate('created_at', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Salas Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Room::where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-theater-masks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ingresos Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(\App\Models\Booking::whereDate('created_at', today())->sum('total_amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-film fa-3x mb-3"></i>
                    <h5 class="card-title">Gestionar Películas</h5>
                    <p class="card-text">Añadir, editar y eliminar películas</p>
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-light">Ir a Películas</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-theater-masks fa-3x mb-3"></i>
                    <h5 class="card-title">Gestionar Salas</h5>
                    <p class="card-text">Configurar salas y butacas</p>
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-light">Ir a Salas</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h5 class="card-title">Gestionar Funciones</h5>
                    <p class="card-text">Programar horarios</p>
                    <a href="{{ route('admin.showtimes.index') }}" class="btn btn-light">Ir a Funciones</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                    <h5 class="card-title">Ver Reservas</h5>
                    <p class="card-text">Gestionar todas las reservas</p>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">Ir a Reservas</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Reservas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Últimas Reservas</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentBookings = \App\Models\Booking::with(['user', 'showtime.movie'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Película</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_code }}</td>
                                        <td>{{ $booking->showtime->movie->title }}</td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->showtime->start_time->format('d/m H:i') }}</td>
                                        <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                                {{ $booking->status === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay reservas recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
