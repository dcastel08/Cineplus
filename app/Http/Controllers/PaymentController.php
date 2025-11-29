<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function show(Booking $booking)
    {
        // Verificar que la reserva pertenece al usuario o es un cajero
        if ($booking->user_id !== Auth::id() && !Auth::user()->isCashier()) {
            abort(403, 'No tienes permisos para ver esta reserva.');
        }

        // Verificar que el pago esté pendiente
        if (!$booking->isPendingPayment()) {
            return redirect()->route('bookings.confirmation', $booking);
        }

        return view('payments.show', compact('booking'));
    }

    public function process(Request $request, Booking $booking)
    {
        \Log::info('=== INICIANDO PAGO ===');
        \Log::info('Método: ' . $request->payment_method);
        \Log::info('Usuario: ' . Auth::user()->email);
        \Log::info('Rol: ' . Auth::user()->role);
        \Log::info('Booking ID: ' . $booking->id);
        \Log::info('Datos recibidos:', $request->all());

        // PERMITIR CAJEROS ACCEDER A CUALQUIER RESERVA
        if ($booking->user_id !== Auth::id() && !Auth::user()->isCashier()) {
            \Log::error('Permiso denegado para usuario: ' . Auth::user()->email);
            abort(403, 'No tienes permisos para procesar este pago.');
        }

        \Log::info('Permisos OK - Procesando pago...');

        // SI ES EFECTIVO - PROCESAR INMEDIATAMENTE
        if ($request->payment_method === 'cash') {
            \Log::info('Procesando EFECTIVO para booking: ' . $booking->id);
            
            try {
                $booking->update([
                    'payment_status' => 'completed',
                    'payment_method' => 'cash',
                    'payment_reference' => 'CASH-' . Str::random(6),
                    'status' => 'confirmed'
                ]);

                \Log::info('Pago en efectivo EXITOSO - Booking actualizado');
                return redirect()->route('payments.success', $booking);

            } catch (\Exception $e) {
                \Log::error('Error en pago efectivo: ' . $e->getMessage());
                return back()->with('error', 'Error al procesar el pago en efectivo.');
            }
        }

        // SI ES TARJETA - validar y procesar
        if ($request->payment_method === 'card') {
            \Log::info('Validando pago con TARJETA...');

            try {
                // Limpiar datos de tarjeta antes de validar
                $cardData = $request->all();
                $cardData['card_number'] = str_replace(' ', '', $request->card_number); // quitar espacios
                $cardData['card_expiry'] = $request->card_expiry; // mantener formato m/y

                \Log::info('Card number limpio: ' . $cardData['card_number']);
                \Log::info('Card expiry: ' . $cardData['card_expiry']);

                // Validar campos de tarjeta con datos limpios
                $validator = Validator::make($cardData, [
                    'card_number' => 'required|digits:16',
                    'card_expiry' => 'required|date_format:m/y',
                    'card_cvc' => 'required|digits:3',
                    'card_holder' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    \Log::error('Validación fallida:', $validator->errors()->toArray());
                    return back()->withErrors($validator)->withInput();
                }

                \Log::info('Validación de tarjeta EXITOSA');

                $booking->update([
                    'payment_status' => 'completed',
                    'payment_method' => 'card',
                    'payment_reference' => 'CARD-' . Str::random(8),
                    'status' => 'confirmed'
                ]);

                \Log::info('Pago con tarjeta EXITOSO - Booking actualizado');
                return redirect()->route('payments.success', $booking);

            } catch (\Exception $e) {
                \Log::error('Error en pago con tarjeta: ' . $e->getMessage());
                return back()->with('error', 'Error al procesar el pago con tarjeta.');
            }
        }

        \Log::error('Método de pago no válido: ' . $request->payment_method);
        return back()->with('error', 'Método de pago no válido.');
    }

    public function success(Booking $booking)
    {
        // Verificar que pertenece al usuario o es un cajero
        if ($booking->user_id !== Auth::id() && !Auth::user()->isCashier()) {
            abort(403, 'No tienes permisos para ver esta reserva.');
        }

        // Verificar pago
        if (!$booking->isPaid()) {
            return redirect()->route('payments.show', $booking);
        }

        return view('payments.success', compact('booking'));
    }
}
