@extends('layouts.app')

@section('title', 'Inicio - CinePlus')

@section('content')
<div class="container mt-4">
    <!-- Banner Principal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-dark text-light p-5 rounded">
                <h1 class="display-4">Bienvenido a CinePlus</h1>
                <p class="lead">Descubre las mejores películas en cartelera</p>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('home') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Buscar Película</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Buscar por título...">
                        </div>
                        <div class="col-md-3">
                            <label for="genre" class="form-label">Género</label>
                            <select class="form-select" id="genre" name="genre">
                                <option value="">Todos los géneros</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>
                                        {{ $genre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="release_date" class="form-label">Estrenos desde</label>
                            <input type="date" class="form-control" id="release_date" name="release_date" 
                                   value="{{ request('release_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(request()->anyFilled(['search', 'genre', 'release_date']))
                    <div class="mt-3">
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar filtros
                        </a>
                        <small class="text-muted ms-2">
                            {{ $movies->count() }} película(s) encontrada(s)
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cartelera -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center">Cartelera</h2>
            <p class="text-center text-muted">Descubre las mejores películas en CinePlus</p>
        </div>
    </div>

    @if($movies->count() > 0)
    <div class="row">
        @foreach($movies as $movie)
        <div class="col-md-4 mb-4">
            <div class="card movie-card h-100">
                <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                     class="card-img-top" alt="{{ $movie->title }}" style="height: 450px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $movie->title }}</h5>
                    <p class="card-text">{{ Str::limit($movie->description, 100) }}</p>
                    <p class="text-muted">
                        <small>
                            <i class="fas fa-clock"></i> {{ $movie->duration }} min | 
                            <i class="fas fa-tag"></i> {{ $movie->genre }}
                        </small>
                    </p>
                    <div class="d-grid">
                        <a href="{{ route('movies.show', $movie) }}" class="btn btn-primary">Ver Detalles y Horarios</a>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        Estreno: {{ $movie->release_date->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-film fa-3x text-muted mb-3"></i>
                <h4>No se encontraron películas</h4>
                <p class="text-muted">No hay películas que coincidan con tu búsqueda.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Ver toda la cartelera</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
