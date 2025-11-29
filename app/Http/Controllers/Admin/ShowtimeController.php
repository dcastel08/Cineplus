<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{
    public function index()
    {
        $showtimes = Showtime::with(['movie', 'room'])
            ->orderBy('start_time', 'desc')
            ->get();
            
        return view('admin.showtimes.index', compact('showtimes'));
    }

    public function create()
    {
        $movies = Movie::where('is_active', true)->orderBy('title')->get();
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.showtimes.create', compact('movies', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
        ]);

        // Calcular hora de fin basado en la duración de la película
        $movie = Movie::find($request->movie_id);
        $startTime = $request->start_time;
        $endTime = (new \Carbon\Carbon($startTime))->addMinutes($movie->duration + 15); // +15 minutos para limpieza

        // Verificar conflictos de horarios en la misma sala
        $conflictingShowtime = Showtime::where('room_id', $request->room_id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->where('is_active', true)
            ->first();

        if ($conflictingShowtime) {
            return back()->withErrors([
                'start_time' => 'La sala ya está ocupada en este horario. Conflicto con: ' . 
                              $conflictingShowtime->movie->title . ' (' . 
                              $conflictingShowtime->start_time->format('d/m H:i') . ')'
            ])->withInput();
        }

        Showtime::create([
            'movie_id' => $request->movie_id,
            'room_id' => $request->room_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Función creada exitosamente.');
    }

    public function edit(Showtime $showtime)
    {
        $movies = Movie::where('is_active', true)->orderBy('title')->get();
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.showtimes.edit', compact('showtime', 'movies', 'rooms'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Calcular nueva hora de fin
        $movie = Movie::find($request->movie_id);
        $startTime = $request->start_time;
        $endTime = (new \Carbon\Carbon($startTime))->addMinutes($movie->duration + 15);

        // Verificar conflictos (excluyendo la función actual)
        $conflictingShowtime = Showtime::where('room_id', $request->room_id)
            ->where('id', '!=', $showtime->id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->where('is_active', true)
            ->first();

        if ($conflictingShowtime) {
            return back()->withErrors([
                'start_time' => 'La sala ya está ocupada en este horario. Conflicto con: ' . 
                              $conflictingShowtime->movie->title . ' (' . 
                              $conflictingShowtime->start_time->format('d/m H:i') . ')'
            ])->withInput();
        }

        $showtime->update([
            'movie_id' => $request->movie_id,
            'room_id' => $request->room_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $request->price,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Función actualizada exitosamente.');
    }

    public function destroy(Showtime $showtime)
    {
        // Verificar si hay reservas asociadas
        if ($showtime->bookings()->exists()) {
            return redirect()->route('admin.showtimes.index')
                ->with('error', 'No se puede eliminar la función porque tiene reservas asociadas.');
        }

        $showtime->delete();

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Función eliminada exitosamente.');
    }
}