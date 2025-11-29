<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::where('is_active', true);
        
        // Búsqueda por título
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por género
        if ($request->has('genre') && $request->genre != '') {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }
        
        // Filtro por fecha de estreno
        if ($request->has('release_date') && $request->release_date != '') {
            $query->whereDate('release_date', '>=', $request->release_date);
        }

        $movies = $query->orderBy('release_date', 'desc')->get();
        
        // Obtener géneros únicos para el filtro
        $genres = Movie::where('is_active', true)
            ->distinct()
            ->pluck('genre')
            ->flatMap(function ($genre) {
                return explode(', ', $genre);
            })
            ->unique()
            ->sort()
            ->values();

        return view('home', compact('movies', 'genres'));
    }

    public function show(Movie $movie)
    {
        $showtimes = $movie->showtimes()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->with('room')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($showtime) {
                return $showtime->start_time->format('Y-m-d');
            });

        return view('movies.show', compact('movie', 'showtimes'));
    }
}
