@extends('layouts.app')

@section('title', 'Crear Sala - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Crear Nueva Sala</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rooms.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre de la Sala *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Ej: Sala 1 - Premium" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rows" class="form-label">Número de Filas *</label>
                                        <input type="number" class="form-control @error('rows') is-invalid @enderror" 
                                               id="rows" name="rows" value="{{ old('rows', 8) }}" 
                                               min="1" max="20" required>
                                        @error('rows')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Máximo 20 filas</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="columns" class="form-label">Número de Columnas *</label>
                                        <input type="number" class="form-control @error('columns') is-invalid @enderror" 
                                               id="columns" name="columns" value="{{ old('columns', 10) }}" 
                                               min="1" max="25" required>
                                        @error('columns')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Máximo 25 columnas</small>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Información</h6>
                                    <ul class="mb-0">
                                        <li>Las butacas se crearán automáticamente</li>
                                        <li>Las primeras 2 filas serán tipo VIP</li>
                                        <li>Total de butacas: <span id="totalSeats">80</span></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Vista Previa de la Sala</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="bg-dark text-light py-2 rounded small">
                                                <strong>PANTALLA</strong>
                                            </div>
                                        </div>
                                        
                                        <div id="roomPreview" class="d-flex flex-column align-items-center">
                                            <!-- La vista previa se generará con JavaScript -->
                                        </div>

                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <div class="d-flex justify-content-center gap-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat available me-1" style="width: 15px; height: 15px;"></div>
                                                        <small>Regular</small>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat vip me-1" style="width: 15px; height: 15px;"></div>
                                                        <small>VIP</small>
                                                    </div>
                                                </div>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Sala y Butacas
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
    const rowsInput = document.getElementById('rows');
    const columnsInput = document.getElementById('columns');
    const totalSeatsSpan = document.getElementById('totalSeats');
    const roomPreview = document.getElementById('roomPreview');

    function updatePreview() {
        const rows = parseInt(rowsInput.value) || 0;
        const columns = parseInt(columnsInput.value) || 0;
        const totalSeats = rows * columns;
        
        totalSeatsSpan.textContent = totalSeats;

        // Generar vista previa de la sala
        roomPreview.innerHTML = '';
        
        for (let row = 1; row <= rows; row++) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'd-flex justify-content-center mb-1';
            
            for (let col = 1; col <= columns; col++) {
                const seatType = row <= 2 ? 'vip' : 'available';
                const seatCode = String.fromCharCode(64 + row) + col;
                
                const seatDiv = document.createElement('div');
                seatDiv.className = `seat ${seatType} mx-1`;
                seatDiv.style.width = '20px';
                seatDiv.style.height = '20px';
                seatDiv.style.fontSize = '8px';
                seatDiv.style.display = 'flex';
                seatDiv.style.alignItems = 'center';
                seatDiv.style.justifyContent = 'center';
                seatDiv.title = `Butaca ${seatCode}`;
                
                roomPreview.appendChild(rowDiv);
                rowDiv.appendChild(seatDiv);
            }
        }
    }

    rowsInput.addEventListener('input', updatePreview);
    columnsInput.addEventListener('input', updatePreview);

    // Inicializar vista previa
    updatePreview();
});
</script>
@endpush
@endsection