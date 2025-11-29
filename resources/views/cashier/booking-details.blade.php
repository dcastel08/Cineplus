@extends('layouts.app')

@section('title', 'Detalles de Reserva - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Reserva Validada</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                        <h4 class="mt-3 text-success">Reserva Válida</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información de la Reserva</h6>
                            <ul class="list-unstyled">
                                <li><strong>Código:</strong> {{ $booking->booking_code }}</li>
                                <li><strong>Estado:</strong> 
                                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'warning' : 'success' }}">
                                        {{ $booking->status === 'confirmed' ? 'Pendiente' : 'Utilizada' }}
                                    </span>
                                </li>
                                <li><strong>Fecha Reserva:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Información del Cliente</h6>
                            <ul class="list-unstyled">
                                <li><strong>Nombre:</strong> {{ $booking->user->name }}</li>
                                <li><strong>Email:</strong> {{ $booking->user->email }}</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Detalles de la Función</h6>
                            <ul class="list-unstyled">
                                <li><strong>Película:</strong> {{ $booking->showtime->movie->title }}</li>
                                <li><strong>Sala:</strong> {{ $booking->showtime->room->name }}</li>
                                <li><strong>Horario:</strong> {{ $booking->showtime->start_time->format('d/m/Y H:i') }}</li>
                                <li><strong>Precio por ticket:</strong> ${{ number_format($booking->showtime->price, 2) }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Detalles de Butacas</h6>
                            <div class="mb-3">
                                @foreach($booking->seats as $seat)
                                    <span class="badge bg-primary me-1 mb-1">
                                        {{ $seat->seat_code }} 
                                        <small>({{ $seat->type }})</small>
                                    </span>
                                @endforeach
                            </div>
                            <p><strong>Total Tickets:</strong> {{ $booking->ticket_count }}</p>
                            <p><strong>Total Pagado:</strong> <span class="text-success h5">${{ number_format($booking->total_amount, 2) }}</span></p>
                        </div>
                    </div>

                    @if($booking->status === 'confirmed')
                    <div class="alert alert-warning mt-4">
                        <h6><i class="fas fa-exclamation-triangle"></i> Acción Requerida</h6>
                        <p>Esta reserva está pendiente de uso. Marca como utilizada cuando el cliente ingrese a la sala.</p>
                        
                        <form action="{{ route('cashier.booking.mark-used', $booking) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Marcar como Utilizada
                            </button>
                            <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
                        </form>
                    </div>
                    @else
                    <div class="alert alert-success mt-4">
                        <h6><i class="fas fa-info-circle"></i> Reserva Utilizada</h6>
                        <p>Esta reserva ya fue marcada como utilizada.</p>
                        <a href="{{ route('cashier.dashboard') }}" class="btn btn-primary">Validar Otra Reserva</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection