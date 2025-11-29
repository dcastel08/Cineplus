<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.room', 'seats'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'showtime.movie', 'showtime.room', 'seats']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Reserva cancelada exitosamente.');
    }
}