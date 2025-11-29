<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount('seats')->orderBy('created_at', 'desc')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'rows' => 'required|integer|min:1|max:20',
            'columns' => 'required|integer|min:1|max:25',
        ]);

        // Crear la sala
        $room = Room::create($request->all());

        // Crear butacas automáticamente
        $this->createSeats($room, $request->rows, $request->columns);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Sala creada exitosamente con ' . ($request->rows * $request->columns) . ' butacas.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'rows' => 'required|integer|min:1|max:20',
            'columns' => 'required|integer|min:1|max:25',
            'is_active' => 'boolean',
        ]);

        $room->update($request->all());

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Sala actualizada exitosamente.');
    }

    public function destroy(Room $room)
    {
        // Verificar si hay funciones asociadas
        if ($room->showtimes()->exists()) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'No se puede eliminar la sala porque tiene funciones asociadas.');
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Sala eliminada exitosamente.');
    }

    private function createSeats(Room $room, $rows, $columns)
    {
        $seats = [];

        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $columns; $col++) {
                $type = 'regular';
                if ($row <= 2) {
                    $type = 'vip'; // Las primeras 2 filas son VIP
                }

                $seats[] = [
                    'room_id' => $room->id,
                    'row_number' => $row,
                    'column_number' => $col,
                    'seat_code' => chr(64 + $row) . $col, // A1, A2, B1, etc.
                    'type' => $type,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Seat::insert($seats);
    }
}