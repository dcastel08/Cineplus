@extends('layouts.app')

@section('title', 'Gestión de Reservas - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Todas las Reservas</h5>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Usuario</th>
                                        <th>Película</th>
                                        <th>Fecha Función</th>
                                        <th>Butacas</th>
                                        <th>Total</th>
                                        <th>Estado Pago</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                    <tr>
                                        <td><strong>{{ $booking->booking_code }}</strong></td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->showtime->movie->title }}</td>
                                        <td>{{ $booking->showtime->start_time->format('d/m H:i') }}</td>
                                        <td>
                                            @foreach($booking->seats as $seat)
                                                <span class="badge bg-secondary">{{ $seat->seat_code }}</span>
                                            @endforeach
                                        </td>
                                        <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->payment_status === 'completed' ? 'success' : 'warning' }}">
                                                {{ $booking->payment_status === 'completed' ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'used' ? 'secondary' : 'warning') }}">
                                                {{ $booking->status === 'confirmed' ? 'Confirmada' : ($booking->status === 'used' ? 'Utilizada' : 'Cancelada') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay reservas en el sistema.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection