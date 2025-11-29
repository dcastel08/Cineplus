@extends('layouts.app')

@section('title', 'Mis Reservas - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2>Mis Reservas</h2>
            
            @if($bookings->count() > 0)
                <div class="row">
                    @foreach($bookings as $booking)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>{{ $booking->booking_code }}</strong>
                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                    {{ $booking->status === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $booking->showtime->movie->title }}</h5>
                                <p class="card-text">
                                    <strong>Sala:</strong> {{ $booking->showtime->room->name }}<br>
                                    <strong>Fecha:</strong> {{ $booking->showtime->start_time->format('d/m/Y H:i') }}<br>
                                    <strong>Butacas:</strong> 
                                    @foreach($booking->seats as $seat)
                                        {{ $seat->seat_code }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary">${{ number_format($booking->total_amount, 2) }}</strong>
                                    <small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                    <h4>No tienes reservas</h4>
                    <p class="text-muted">Cuando hagas una reserva, aparecerá aquí.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Ver Cartelera</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection