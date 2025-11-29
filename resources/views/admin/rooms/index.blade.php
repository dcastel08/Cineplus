@extends('layouts.app')

@section('title', 'Gestionar Salas - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestionar Salas</h5>
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Sala
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
                                    <th>Nombre</th>
                                    <th>Configuración</th>
                                    <th>Butacas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms as $room)
                                <tr>
                                    <td>
                                        <strong>{{ $room->name }}</strong>
                                    </td>
                                    <td>
                                        {{ $room->rows }} filas × {{ $room->columns }} columnas
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $room->seats_count }} butacas</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $room->is_active ? 'success' : 'secondary' }}">
                                            {{ $room->is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.rooms.edit', $room) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.rooms.destroy', $room) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta sala? Se eliminarán todas sus butacas.')">
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

                    @if($rooms->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-theater-masks fa-3x text-muted mb-3"></i>
                        <h5>No hay salas registradas</h5>
                        <p class="text-muted">Comienza añadiendo tu primera sala.</p>
                        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Sala
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection