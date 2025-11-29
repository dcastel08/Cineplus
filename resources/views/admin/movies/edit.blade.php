@extends('layouts.app')

@section('title', 'Editar Película - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Película: {{ $movie->title }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.movies.update', $movie) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Título *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $movie->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="genre" class="form-label">Género *</label>
                                        <input type="text" class="form-control @error('genre') is-invalid @enderror" 
                                               id="genre" name="genre" value="{{ old('genre', $movie->genre) }}" required>
                                        @error('genre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="duration" class="form-label">Duración (minutos) *</label>
                                        <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                               id="duration" name="duration" value="{{ old('duration', $movie->duration) }}" min="1" required>
                                        @error('duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="release_date" class="form-label">Fecha de Estreno *</label>
                                        <input type="date" class="form-control @error('release_date') is-invalid @enderror" 
                                               id="release_date" name="release_date" value="{{ old('release_date', $movie->release_date->format('Y-m-d')) }}" required>
                                        @error('release_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="director" class="form-label">Director *</label>
                                    <input type="text" class="form-control @error('director') is-invalid @enderror" 
                                           id="director" name="director" value="{{ old('director', $movie->director) }}" required>
                                    @error('director')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="cast" class="form-label">Reparto *</label>
                                    <input type="text" class="form-control @error('cast') is-invalid @enderror" 
                                           id="cast" name="cast" value="{{ old('cast', $movie->cast) }}" required>
                                    @error('cast')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Descripción *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" required>{{ old('description', $movie->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="poster_url" class="form-label">URL del Poster</label>
                                        <input type="url" class="form-control @error('poster_url') is-invalid @enderror" 
                                               id="poster_url" name="poster_url" value="{{ old('poster_url', $movie->poster_url) }}">
                                        @error('poster_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="trailer_url" class="form-label">URL del Tráiler</label>
                                        <input type="url" class="form-control @error('trailer_url') is-invalid @enderror" 
                                               id="trailer_url" name="trailer_url" value="{{ old('trailer_url', $movie->trailer_url) }}">
                                        @error('trailer_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $movie->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Película activa (visible en cartelera)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Vista Previa</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="posterPreview" 
                                             src="{{ $movie->poster_url ?: 'https://via.placeholder.com/300x450?text=Poster+de+película' }}" 
                                             alt="Vista previa del poster" 
                                             class="img-fluid rounded mb-3"
                                             style="max-height: 300px;">
                                        <h6 id="titlePreview">{{ $movie->title }}</h6>
                                        <p id="genrePreview" class="text-muted small">{{ $movie->genre }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Película
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
    // Vista previa en tiempo real
    const titleInput = document.getElementById('title');
    const genreInput = document.getElementById('genre');
    const posterUrlInput = document.getElementById('poster_url');
    const titlePreview = document.getElementById('titlePreview');
    const genrePreview = document.getElementById('genrePreview');
    const posterPreview = document.getElementById('posterPreview');

    titleInput.addEventListener('input', function() {
        titlePreview.textContent = this.value || 'Título de la película';
    });

    genreInput.addEventListener('input', function() {
        genrePreview.textContent = this.value || 'Género';
    });

    posterUrlInput.addEventListener('input', function() {
        if (this.value) {
            posterPreview.src = this.value;
        } else {
            posterPreview.src = 'https://via.placeholder.com/300x450?text=Poster+de+película';
        }
    });
});
</script>
@endpush
@endsection