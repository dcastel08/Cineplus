<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    // -----------------------------
    // DASHBOARD DEL CAJERO
    // -----------------------------
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role !== 'cashier') {
            abort(403, 'No tienes permisos de cajero.');
        }

        $today = now()->format('Y-m-d');

        $todayBookings = Booking::whereDate('created_at', $today)->get();
        $todaySales = $todayBookings->sum('total_amount');
        $todayTickets = $todayBookings->sum('ticket_count');

        $todayShowtimes = Showtime::with(['movie', 'room'])
            ->whereDate('start_time', $today)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $upcomingBookings = Booking::with(['user', 'showtime.movie', 'seats'])
            ->whereHas('showtime', function($query) use ($today) {
                $query->whereDate('start_time', $today);
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('cashier.dashboard', compact(
            'todaySales', 
            'todayTickets',
            'todayBookings',
            'todayShowtimes',
            'upcomingBookings'
        ));
    }

    // -----------------------------
    // RESERVAS DE HOY
    // -----------------------------
    public function bookingsToday()
    {
        $today = now()->format('Y-m-d');

        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.room', 'seats'])
            ->whereHas('showtime', function($query) use ($today) {
                $query->whereDate('start_time', $today);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.bookings-today', compact('bookings'));
    }

    // -----------------------------
    // FORMULARIO PARA VALIDAR RESERVA
    // -----------------------------
    public function showValidationForm()
    {
        return view('cashier.validate-booking');
    }

    // -----------------------------
    // VALIDAR CÓDIGO DE RESERVA
    // -----------------------------
    public function validateBooking(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string|exists:bookings,booking_code'
        ]);

        $booking = Booking::with(['user', 'showtime.movie', 'showtime.room', 'seats'])
            ->where('booking_code', $request->booking_code)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Código de reserva no encontrado.');
        }

        if ($booking->showtime->start_time->isPast()) {
            return back()->with('error', 'La función ya ha finalizado.');
        }

        if ($booking->isUsed()) {
            return back()->with('error', 'Esta reserva ya fue utilizada.');
        }

        return view('cashier.booking-details', compact('booking'));
    }

    // -----------------------------
    // MARCAR RESERVA COMO USADA
    // -----------------------------
    public function markAsUsed(Booking $booking)
    {
        // Verificar permisos
        if (!Auth::check() || Auth::user()->role !== 'cashier') {
            abort(403, 'No tienes permisos de cajero.');
        }

        // Verificar si la función ya pasó
        if ($booking->showtime->start_time->isPast()) {
            return back()->with('error', 'No se puede marcar como usada porque la función ya finalizó.');
        }

        // Actualizar estado a 'used'
        $booking->update(['status' => 'used']);

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Reserva marcada como utilizada exitosamente.');
    }

    // -----------------------------
    // REPORTE DE VENTAS
    // -----------------------------
    public function salesReport()
    {
        $today = now()->format('Y-m-d');

        // Totales
        $salesData = Booking::whereDate('bookings.created_at', $today)
            ->select(
                DB::raw('SUM(bookings.total_amount) as total_sales'),
                DB::raw('SUM(bookings.ticket_count) as total_tickets'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('AVG(bookings.total_amount) as average_sale')
            )
            ->first();

        // Ventas por hora
        $hourlySales = Booking::whereDate('bookings.created_at', $today)
            ->select(
                DB::raw('HOUR(bookings.created_at) as hour'),
                DB::raw('SUM(bookings.total_amount) as sales'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy(DB::raw('HOUR(bookings.created_at)'))
            ->orderBy('hour')
            ->get();

        // Ventas por película
        $movieSales = Booking::whereDate('bookings.created_at', $today)
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->select(
                'movies.title',
                DB::raw('SUM(bookings.total_amount) as sales'),
                DB::raw('SUM(bookings.ticket_count) as tickets')
            )
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('sales')
            ->get();

        return view('cashier.sales-report', compact('salesData', 'hourlySales', 'movieSales'));
    }
}
