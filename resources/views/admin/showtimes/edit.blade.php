@extends('layouts.app')

@section('title', 'Editar Función - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Función</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.showtimes.update', $showtime) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="movie_id" class="form-label">Película *</label>
                                    <select class="form-select @error('movie_id') is-invalid @enderror" 
                                            id="movie_id" name="movie_id" required>
                                        <option value="">Selecciona una película</option>
                                        @foreach($movies as $movie)
                                            <option value="{{ $movie->id }}" 
                                                    {{ old('movie_id', $showtime->movie_id) == $movie->id ? 'selected' : '' }}
                                                    data-duration="{{ $movie->duration }}">
                                                {{ $movie->title }} ({{ $movie->duration }} min)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('movie_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room_id" class="form-label">Sala *</label>
                                    <select class="form-select @error('room_id') is-invalid @enderror" 
                                            id="room_id" name="room_id" required>
                                        <option value="">Selecciona una sala</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" 
                                                    {{ old('room_id', $showtime->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->name }} ({{ $room->rows }}x{{ $room->columns }} - {{ $room->seats->count() }} butacas)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Fecha y Hora de Inicio *</label>
                                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" name="start_time" 
                                           value="{{ old('start_time', $showtime->start_time->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio por Ticket ($) *</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" 
                                           value="{{ old('price', $showtime->price) }}" 
                                           step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $showtime->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Función activa (visible al público)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Información Actual</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>Película:</strong><br>
                                            <div class="d-flex align-items-center mt-1">
                                                <img src="{{ $showtime->movie->poster_url ?? 'https://via.placeholder.com/50x75?text=No+Image' }}" 
                                                     alt="{{ $showtime->movie->title }}" 
                                                     style="width: 50px; height: 75px; object-fit: cover;" 
                                                     class="rounded me-2">
                                                <div>
                                                    {{ $showtime->movie->title }}<br>
                                                    <small class="text-muted">{{ $showtime->movie->duration }} min</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Sala:</strong><br>
                                            {{ $showtime->room->name }}
                                        </div>

                                        <div class="mb-3">
                                            <strong>Horario Original:</strong><br>
                                            {{ $showtime->start_time->format('d/m/Y H:i') }} - {{ $showtime->end_time->format('H:i') }}
                                        </div>

                                        <div class="mb-3">
                                            <strong>Reservas:</strong><br>
                                            {{ $showtime->bookings->count() }} reservas - 
                                            {{ $showtime->bookings->sum('ticket_count') }} tickets vendidos
                                        </div>

                                        @if($showtime->bookings->count() > 0)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <small>Esta función tiene reservas activas. Los cambios pueden afectar a los clientes.</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.showtimes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Función
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection