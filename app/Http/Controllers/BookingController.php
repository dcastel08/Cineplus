<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function show(Showtime $showtime)
    {
        $room = $showtime->room;
        $seats = $room->seats()->where('is_active', true)->get();
        $bookedSeats = $showtime->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('seats')
            ->get()
            ->flatMap(function ($booking) {
                return $booking->seats;
            });

        return view('bookings.show', compact('showtime', 'seats', 'bookedSeats'));
    }

    public function store(Request $request, Showtime $showtime)
    {
        $request->validate([
            'seats' => 'required|array|min:1',
            'seats.*' => 'exists:seats,id',
        ]);

        $selectedSeats = Seat::whereIn('id', $request->seats)->get();

        // Verificar disponibilidad
        foreach ($selectedSeats as $seat) {
            if ($showtime->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereHas('seats', function ($query) use ($seat) {
                    $query->where('seat_id', $seat->id);
                })->exists()) {
                return back()->withErrors(['seats' => 'Uno o más asientos seleccionados ya están ocupados.']);
            }
        }

        // Crear reserva como "pending" para ir al pago
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'showtime_id' => $showtime->id,
            'ticket_count' => count($request->seats),
            'total_amount' => $showtime->price * count($request->seats),
            'status' => 'pending', // Ya no se confirma hasta que pague
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $booking->seats()->attach($request->seats);

        // Redirigir al proceso de pago
        return redirect()->route('payments.show', $booking);
    }

    public function confirmation(Booking $booking)
    {
        return view('bookings.confirmation', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = Auth::user()->bookings()
            ->with(['showtime.movie', 'seats'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bookings.my-bookings', compact('bookings'));
    }
}
