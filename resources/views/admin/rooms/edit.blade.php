@extends('layouts.app')

@section('title', 'Editar Sala - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Sala: {{ $room->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre de la Sala *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $room->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rows" class="form-label">Número de Filas</label>
                                        <input type="number" class="form-control" 
                                               id="rows" value="{{ $room->rows }}" disabled>
                                        <small class="form-text text-muted">No se puede modificar después de crear</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="columns" class="form-label">Número de Columnas</label>
                                        <input type="number" class="form-control" 
                                               id="columns" value="{{ $room->columns }}" disabled>
                                        <small class="form-text text-muted">No se puede modificar después de crear</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Sala activa (disponible para funciones)
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Información de la Sala</h6>
                                    <ul class="mb-0">
                                        <li><strong>Configuración:</strong> {{ $room->rows }} filas × {{ $room->columns }} columnas</li>
                                        <li><strong>Total de butacas:</strong> {{ $room->seats->count() }}</li>
                                        <li><strong>Butacas VIP:</strong> {{ $room->seats->where('type', 'vip')->count() }}</li>
                                        <li><strong>Butacas regulares:</strong> {{ $room->seats->where('type', 'regular')->count() }}</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Mapa Actual de la Sala</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="bg-dark text-light py-2 rounded small">
                                                <strong>PANTALLA</strong>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex flex-column align-items-center">
                                            @php
                                                $currentRow = null;
                                            @endphp
                                            
                                            @foreach($room->seats->sortBy('row_number')->sortBy('column_number') as $seat)
                                                @if($currentRow !== $seat->row_number)
                                                    @if($currentRow !== null)
                                                        </div>
                                                    @endif
                                                    <div class="d-flex justify-content-center mb-1">
                                                    @php $currentRow = $seat->row_number; @endphp
                                                @endif
                                                
                                                <div class="seat {{ $seat->type === 'vip' ? 'vip' : 'available' }} mx-1"
                                                     style="width: 20px; height: 20px; font-size: 8px;"
                                                     title="Butaca {{ $seat->seat_code }} - {{ $seat->type }}">
                                                </div>
                                            @endforeach
                                            </div>
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

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Sala
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection