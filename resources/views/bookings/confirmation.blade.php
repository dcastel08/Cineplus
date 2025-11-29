@extends('layouts.app')

@section('title', 'Confirmación de Reserva - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">¡Reserva Confirmada!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información de la Reserva</h5>
                            <p><strong>Código de Reserva:</strong> {{ $booking->booking_code }}</p>
                            <p><strong>Película:</strong> {{ $booking->showtime->movie->title }}</p>
                            <p><strong>Sala:</strong> {{ $booking->showtime->room->name }}</p>
                            <p><strong>Fecha:</strong> {{ $booking->showtime->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Detalles de Butacas</h5>
                            <ul>
                                @foreach($booking->seats as $seat)
                                    <li>Butaca {{ $seat->seat_code }}</li>
                                @endforeach
                            </ul>
                            <p><strong>Total Pagado:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle"></i> Instrucciones</h6>
                        <ul class="mb-0">
                            <li>Presenta este código en taquilla: <strong>{{ $booking->booking_code }}</strong></li>
                            <li>Llega 30 minutos antes de la función</li>
                            <li>Trae tu identificación</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">Volver al Inicio</a>
                        <a href="{{ route('bookings.my-bookings') }}" class="btn btn-outline-primary">Ver Mis Reservas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection