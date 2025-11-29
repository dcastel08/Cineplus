@extends('layouts.app')

@section('title', 'Gestionar Funciones - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestionar Funciones</h5>
                    <a href="{{ route('admin.showtimes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Función
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Película</th>
                                    <th>Sala</th>
                                    <th>Fecha y Hora</th>
                                    <th>Precio</th>
                                    <th>Butacas Ocupadas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($showtimes as $showtime)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $showtime->movie->poster_url ?? 'https://via.placeholder.com/40x60?text=No+Image' }}" 
                                                 alt="{{ $showtime->movie->title }}" 
                                                 style="width: 40px; height: 60px; object-fit: cover;" 
                                                 class="rounded me-2">
                                            <div>
                                                <strong>{{ $showtime->movie->title }}</strong><br>
                                                <small class="text-muted">{{ $showtime->movie->duration }} min</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $showtime->room->name }}</td>
                                    <td>
                                        <strong>{{ $showtime->start_time->format('d/m/Y') }}</strong><br>
                                        <small>{{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($showtime->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $occupiedSeats = $showtime->bookings->sum('ticket_count');
                                            $totalSeats = $showtime->room->seats->count();
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $occupiedSeats/$totalSeats > 0.8 ? 'bg-danger' : ($occupiedSeats/$totalSeats > 0.5 ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($occupiedSeats/$totalSeats) * 100 }}%"
                                                 aria-valuenow="{{ $occupiedSeats }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $totalSeats }}">
                                                {{ $occupiedSeats }}/{{ $totalSeats }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $showtime->is_active ? 'success' : 'secondary' }}">
                                            {{ $showtime->is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            @if($showtime->start_time->isPast())
                                                <span class="text-danger">Finalizada</span>
                                            @elseif($showtime->start_time->isToday())
                                                <span class="text-warning">Hoy</span>
                                            @else
                                                <span class="text-success">Próxima</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('bookings.show', $showtime) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank"
                                               title="Ver butacas">
                                                <i class="fas fa-chair"></i>
                                            </a>
                                            <a href="{{ route('admin.showtimes.edit', $showtime) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.showtimes.destroy', $showtime) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta función?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($showtimes->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h5>No hay funciones programadas</h5>
                        <p class="text-muted">Comienza programando tu primera función.</p>
                        <a href="{{ route('admin.showtimes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Función
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection