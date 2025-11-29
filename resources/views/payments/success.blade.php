@extends('layouts.app')

@section('title', 'Pago Exitoso - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> ¡Pago Exitoso!</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-check fa-3x"></i>
                        </div>
                        <h3 class="mt-3 text-success">Pago Completado</h3>
                        <p class="lead">Tu reserva ha sido confirmada exitosamente</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información del Pago</h6>
                            <ul class="list-unstyled">
                                <li><strong>Método de Pago:</strong> {{ $booking->getPaymentMethodText() }}</li>
                                <li><strong>Referencia:</strong> {{ $booking->payment_reference }}</li>
                                <li><strong>Total Pagado:</strong> ${{ number_format($booking->total_amount, 2) }}</li>
                                <li><strong>Fecha de Pago:</strong> {{ $booking->updated_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de la Reserva</h6>
                            <ul class="list-unstyled">
                                <li><strong>Código:</strong> {{ $booking->booking_code }}</li>
                                <li><strong>Película:</strong> {{ $booking->showtime->movie->title }}</li>
                                <li><strong>Sala:</strong> {{ $booking->showtime->room->name }}</li>
                                <li><strong>Horario:</strong> {{ $booking->showtime->start_time->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle"></i> Instrucciones</h6>
                        <ul class="mb-0">
                            <li>Presenta tu código de reserva en taquilla: <strong>{{ $booking->booking_code }}</strong></li>
                            <li>Llega al menos 30 minutos antes de la función</li>
                            <li>Trae una identificación oficial</li>
                            <li>Guarda este comprobante para cualquier aclaración</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('payments.download-ticket', $booking) }}" class="btn btn-primary me-md-2">
                            <i class="fas fa-download"></i> Descargar Ticket
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-film"></i> Ver Más Películas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection