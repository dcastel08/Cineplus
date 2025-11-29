@extends('layouts.app')

@section('title', 'Dashboard Cajero - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1>Dashboard Cajero</h1>
            <p class="lead">Bienvenido, {{ Auth::user()->name }} - {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Estadísticas del Día -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ventas Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($todaySales, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Tickets Vendidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayTickets }}</div>
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
                                Reservas Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayBookings->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Funciones Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayShowtimes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-film fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Validar Reserva -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-qrcode"></i> Validar Reserva</h5>
                </div>
                <div class="card-body">
                    <!-- 🔹 FORMULARIO ACTUALIZADO: GET hacia showValidationForm -->
                    <form action="{{ route('cashier.validation.form') }}" method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-lg" 
                                   name="booking_code" 
                                   placeholder="Ingresa el código de reserva (Ej: CINEABC123)" 
                                   required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i> Validar Código
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Funciones de Hoy -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Funciones de Hoy</h5>
                    <span class="badge bg-primary">{{ $todayShowtimes->count() }}</span>
                </div>
                <div class="card-body">
                    @if($todayShowtimes->count() > 0)
                        <div class="list-group">
                            @foreach($todayShowtimes as $showtime)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $showtime->movie->title }}</h6>
                                    <small class="text-{{ $showtime->start_time->isPast() ? 'danger' : 'success' }}">
                                        {{ $showtime->start_time->format('H:i') }}
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <small>Sala: {{ $showtime->room->name }}</small> | 
                                    <small>Precio: ${{ number_format($showtime->price, 2) }}</small>
                                </p>
                                <small class="text-muted">
                                    Butacas ocupadas: 
                                    {{ $showtime->bookings->sum('ticket_count') }}/{{ $showtime->room->seats->count() }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay funciones programadas para hoy.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Últimas Reservas -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Últimas Reservas</h5>
                    <a href="{{ route('cashier.bookings.today') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Película</th>
                                        <th>Tickets</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingBookings as $booking)
                                    <tr>
                                        <td>
                                            <small><strong>{{ $booking->booking_code }}</strong></small>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($booking->showtime->movie->title, 20) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $booking->ticket_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'secondary' }}">
                                                {{ $booking->status === 'confirmed' ? 'Confirmada' : 'Usada' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay reservas para hoy.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-list fa-2x mb-3"></i>
                    <h6 class="card-title">Todas las Reservas de Hoy</h6>
                    <a href="{{ route('cashier.bookings.today') }}" class="btn btn-light btn-sm">Ver Reservas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-2x mb-3"></i>
                    <h6 class="card-title">Reporte de Ventas</h6>
                    <a href="{{ route('cashier.sales.report') }}" class="btn btn-light btn-sm">Ver Reporte</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-qrcode fa-2x mb-3"></i>
                    <h6 class="card-title">Validar Reserva</h6>
                    <small>Usa el formulario superior</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
