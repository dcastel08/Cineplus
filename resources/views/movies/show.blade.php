@extends('layouts.app')

@section('title', $movie->title . ' - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                 class="img-fluid rounded" alt="{{ $movie->title }}">
        </div>
        <div class="col-md-8">
            <h1>{{ $movie->title }}</h1>
            <p class="lead">{{ $movie->description }}</p>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <p><strong>Género:</strong> {{ $movie->genre }}</p>
                    <p><strong>Duración:</strong> {{ $movie->duration }} minutos</p>
                    <p><strong>Director:</strong> {{ $movie->director }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Reparto:</strong> {{ $movie->cast }}</p>

                    <!-- ✔ CORREGIDO: usar parse para evitar errores -->
                    <p><strong>Fecha de Estreno:</strong> 
                        {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            @if($movie->trailer_url)
            <div class="mt-4">
                <a href="{{ $movie->trailer_url }}" target="_blank" class="btn btn-outline-danger">
                    <i class="fab fa-youtube"></i> Ver Tráiler
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Horarios -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Horarios Disponibles</h3>
            
            @if($showtimes->count() > 0)
                @foreach($showtimes as $date => $dayShowtimes)
                <div class="card mb-3">

                    <!-- ✔ CORREGIDO: isoFormat para español -->
                    <div class="card-header">
                        <h5 class="mb-0">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            @foreach($dayShowtimes as $showtime)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>{{ $showtime->start_time->format('H:i') }}</h6>
                                        <p class="mb-1">Sala {{ $showtime->room->name }}</p>
                                        <p class="text-success mb-2"><strong>${{ number_format($showtime->price, 2) }}</strong></p>
                                        @auth
                                            <a href="{{ route('bookings.show', $showtime) }}" class="btn btn-primary btn-sm">
                                                Reservar
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                                                Iniciar Sesión para Reservar
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    <h5>No hay funciones disponibles</h5>
                    <p class="mb-0">No hay horarios disponibles para esta película en este momento.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
