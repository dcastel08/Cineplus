<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();
        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.movies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'genre' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'cast' => 'required|string',
            'release_date' => 'required|date',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
        ]);

        Movie::create($request->all());

        return redirect()->route('admin.movies.index')
            ->with('success', 'Película creada exitosamente.');
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'genre' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'cast' => 'required|string',
            'release_date' => 'required|date',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $movie->update($request->all());

        return redirect()->route('admin.movies.index')
            ->with('success', 'Película actualizada exitosamente.');
    }

    public function destroy(Movie $movie)
    {
        // Verificar si hay funciones asociadas
        if ($movie->showtimes()->exists()) {
            return redirect()->route('admin.movies.index')
                ->with('error', 'No se puede eliminar la película porque tiene funciones asociadas.');
        }

        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', 'Película eliminada exitosamente.');
    }
}