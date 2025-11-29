<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function download(Booking $booking)
    {
        // Verificar que la reserva pertenece al usuario
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para descargar este ticket.');
        }

        // Verificar que el pago esté completado
        if (!$booking->isPaid()) {
            return redirect()->route('payments.show', $booking)
                ->with('error', 'Debes completar el pago antes de descargar el ticket.');
        }

        $pdf = Pdf::loadView('tickets.pdf', compact('booking'));

        return $pdf->download("ticket-{$booking->booking_code}.pdf");
    }

    public function view(Booking $booking)
    {
        // Verificar que la reserva pertenece al usuario
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para ver este ticket.');
        }

        // Verificar que el pago esté completado
        if (!$booking->isPaid()) {
            return redirect()->route('payments.show', $booking);
        }

        return view('tickets.view', compact('booking'));
    }
}