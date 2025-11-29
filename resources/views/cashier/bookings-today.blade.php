@extends('layouts.app')

@section('title', 'Reservas de Hoy - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Reservas de Hoy - {{ now()->format('d/m/Y') }}</h5>
                    <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Película</th>
                                        <th>Sala</th>
                                        <th>Horario</th>
                                        <th>Cliente</th>
                                        <th>Butacas</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->booking_code }}</strong><br>
                                            <small class="text-muted">{{ $booking->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->showtime->movie->title }}</strong><br>
                                            <small class="text-muted">{{ $booking->showtime->movie->duration }} min</small>
                                        </td>
                                        <td>{{ $booking->showtime->room->name }}</td>
                                        <td>
                                            <small>{{ $booking->showtime->start_time->format('H:i') }}</small>
                                        </td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>
                                            @foreach($booking->seats as $seat)
                                                <span class="badge bg-secondary">{{ $seat->seat_code }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format($booking->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'warning' : 'success' }}">
                                                {{ $booking->status === 'confirmed' ? 'Pendiente' : 'Utilizada' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($booking->status === 'confirmed')
                                                <form action="{{ route('cashier.booking.mark-used', $booking) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Marcar esta reserva como utilizada?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Marcar como usada">
                                                        <i class="fas fa-check"></i> Usada
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-success"><i class="fas fa-check-circle"></i> Usada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6>Total Reservas</h6>
                                            <h4 class="text-primary">{{ $bookings->count() }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6>Total Tickets</h6>
                                            <h4 class="text-success">{{ $bookings->sum('ticket_count') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6>Total Ventas</h6>
                                            <h4 class="text-info">${{ number_format($bookings->sum('total_amount'), 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h5>No hay reservas para hoy</h5>
                            <p class="text-muted">No se han realizado reservas para las funciones de hoy.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection