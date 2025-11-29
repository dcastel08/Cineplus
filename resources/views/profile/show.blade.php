@extends('layouts.app')

@section('title', 'Mi Perfil - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Información del Usuario -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mi Perfil</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p>
                        <span class="badge bg-{{ $user->role === 'client' ? 'info' : 'warning' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Mis Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4>{{ $user->bookings->count() }}</h4>
                            <small class="text-muted">Reservas</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ $user->bookings->sum('ticket_count') }}</h4>
                            <small class="text-muted">Tickets</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Últimas Reservas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mis Últimas Reservas</h5>
                    <a href="{{ route('profile.booking-history') }}" class="btn btn-sm btn-outline-primary">
                        Ver Historial Completo
                    </a>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="list-group">
                            @foreach($recentBookings as $booking)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $booking->showtime->movie->title }}</h6>
                                    <small class="text-{{ $booking->status === 'confirmed' ? 'success' : 'secondary' }}">
                                        {{ $booking->status === 'confirmed' ? 'Confirmada' : 'Usada' }}
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <strong>Código:</strong> {{ $booking->booking_code }} | 
                                    <strong>Fecha:</strong> {{ $booking->showtime->start_time->format('d/m H:i') }}
                                </p>
                                <small class="text-muted">
                                    <strong>Butacas:</strong> 
                                    @foreach($booking->seats as $seat)
                                        {{ $seat->seat_code }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                    | <strong>Total:</strong> ${{ number_format($booking->total_amount, 2) }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-ticket-alt fa-2x text-muted mb-3"></i>
                            <p class="text-muted">No tienes reservas recientes.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Ver Cartelera</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <h6>Historial de Reservas</h6>
                            <a href="{{ route('profile.booking-history') }}" class="btn btn-light btn-sm">Ver Historial</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-film fa-2x mb-2"></i>
                            <h6>Ver Cartelera</h6>
                            <a href="{{ route('home') }}" class="btn btn-light btn-sm">Ir a Cartelera</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection