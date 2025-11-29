@extends('layouts.app')

@section('title', 'Gestionar Películas - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestionar Películas</h5>
                    <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Película
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
                                    <th>Poster</th>
                                    <th>Título</th>
                                    <th>Género</th>
                                    <th>Duración</th>
                                    <th>Estreno</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movies as $movie)
                                <tr>
                                    <td>
                                        <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/60x90?text=No+Image' }}" 
                                             alt="{{ $movie->title }}" 
                                             style="width: 60px; height: 90px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $movie->title }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($movie->description, 50) }}</small>
                                    </td>
                                    <td>{{ $movie->genre }}</td>
                                    <td>{{ $movie->duration }} min</td>
                                    <td>{{ $movie->release_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $movie->is_active ? 'success' : 'secondary' }}">
                                            {{ $movie->is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('movies.show', $movie) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank"
                                               title="Ver en sitio público">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.movies.edit', $movie) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.movies.destroy', $movie) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta película?')">
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

                    @if($movies->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-film fa-3x text-muted mb-3"></i>
                        <h5>No hay películas registradas</h5>
                        <p class="text-muted">Comienza añadiendo tu primera película.</p>
                        <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Película
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection