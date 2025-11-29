@extends('layouts.app')

@section('title', 'Crear Función - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Crear Nueva Función</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.showtimes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="movie_id" class="form-label">Película *</label>
                                    <select class="form-select @error('movie_id') is-invalid @enderror" 
                                            id="movie_id" name="movie_id" required>
                                        <option value="">Selecciona una película</option>
                                        @foreach($movies as $movie)
                                            <option value="{{ $movie->id }}" 
                                                    {{ old('movie_id') == $movie->id ? 'selected' : '' }}
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
                                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
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
                                           value="{{ old('start_time') }}" 
                                           min="{{ now()->format('Y-m-d\TH:i') }}" 
                                           required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio por Ticket ($) *</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', 10.00) }}" 
                                           step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Resumen de la Función</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="functionSummary" class="text-muted">
                                            <p>Selecciona una película y sala para ver el resumen</p>
                                        </div>
                                        
                                        <div id="timeCalculation" class="mt-3" style="display: none;">
                                            <h6>Cálculo de Tiempos:</h6>
                                            <ul class="list-unstyled small">
                                                <li><strong>Inicio:</strong> <span id="startTimePreview"></span></li>
                                                <li><strong>Duración película:</strong> <span id="movieDuration"></span> min</li>
                                                <li><strong>Tiempo limpieza:</strong> 15 min</li>
                                                <li><strong>Fin estimado:</strong> <span id="endTimePreview"></span></li>
                                            </ul>
                                        </div>

                                        <div id="conflictWarning" class="alert alert-warning mt-3" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span id="conflictMessage"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Horarios Ocupados en la Sala</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="roomSchedule">
                                            <p class="text-muted small">Selecciona una sala para ver los horarios ocupados</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.showtimes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Función
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const movieSelect = document.getElementById('movie_id');
    const roomSelect = document.getElementById('room_id');
    const startTimeInput = document.getElementById('start_time');
    const functionSummary = document.getElementById('functionSummary');
    const timeCalculation = document.getElementById('timeCalculation');
    const roomSchedule = document.getElementById('roomSchedule');
    const conflictWarning = document.getElementById('conflictWarning');

    function updateSummary() {
        const selectedMovie = movieSelect.options[movieSelect.selectedIndex];
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const startTime = startTimeInput.value;

        if (selectedMovie.value && selectedRoom.value && startTime) {
            const movieDuration = parseInt(selectedMovie.dataset.duration);
            const startDate = new Date(startTime);
            const endDate = new Date(startDate.getTime() + (movieDuration + 15) * 60000);

            functionSummary.innerHTML = `
                <h6>${selectedMovie.text}</h6>
                <p><strong>Sala:</strong> ${selectedRoom.text.split(' (')[0]}</p>
                <p><strong>Butacas disponibles:</strong> ${selectedRoom.text.match(/\d+ butacas/)[0]}</p>
                <p><strong>Precio:</strong> $${document.getElementById('price').value}</p>
            `;

            document.getElementById('startTimePreview').textContent = startDate.toLocaleString('es-ES');
            document.getElementById('movieDuration').textContent = movieDuration;
            document.getElementById('endTimePreview').textContent = endDate.toLocaleString('es-ES');
            timeCalculation.style.display = 'block';

            // Cargar horarios ocupados de la sala
            loadRoomSchedule(selectedRoom.value);
        } else {
            functionSummary.innerHTML = '<p class="text-muted">Selecciona una película y sala para ver el resumen</p>';
            timeCalculation.style.display = 'none';
            conflictWarning.style.display = 'none';
        }
    }

    function loadRoomSchedule(roomId) {
        fetch(`/admin/rooms/${roomId}/schedule`)
            .then(response => response.json())
            .then(data => {
                if (data.showtimes && data.showtimes.length > 0) {
                    let scheduleHTML = '<div class="small">';
                    data.showtimes.forEach(showtime => {
                        const start = new Date(showtime.start_time).toLocaleString('es-ES');
                        const end = new Date(showtime.end_time).toLocaleString('es-ES');
                        scheduleHTML += `
                            <div class="mb-2 p-2 border rounded">
                                <strong>${showtime.movie.title}</strong><br>
                                ${start} - ${end}
                            </div>
                        `;
                    });
                    scheduleHTML += '</div>';
                    roomSchedule.innerHTML = scheduleHTML;
                } else {
                    roomSchedule.innerHTML = '<p class="text-success small"><i class="fas fa-check-circle"></i> Sala disponible sin conflictos</p>';
                }
            })
            .catch(error => {
                roomSchedule.innerHTML = '<p class="text-muted small">Error al cargar horarios</p>';
            });
    }

    movieSelect.addEventListener('change', updateSummary);
    roomSelect.addEventListener('change', updateSummary);
    startTimeInput.addEventListener('change', updateSummary);
    document.getElementById('price').addEventListener('input', updateSummary);
});
</script>
@endpush
@endsection