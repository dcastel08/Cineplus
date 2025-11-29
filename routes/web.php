<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Illuminate\Support\Str;

// ============================
// PÁGINA PRINCIPAL
// ============================
Route::get('/', [MovieController::class, 'index'])->name('home');

// ============================
// AUTENTICACIÓN
// ============================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================
// PERFIL DE USUARIO
// ============================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/booking-history', [App\Http\Controllers\ProfileController::class, 'bookingHistory'])->name('profile.booking-history');
});

// ============================
// PELÍCULAS PÚBLICAS
// ============================
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

// ============================
// RESERVAS (requieren autenticación)
// ============================
Route::middleware(['auth'])->group(function () {
    Route::get('/showtimes/{showtime}/book', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/showtimes/{showtime}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');
});

// ============================
// RUTAS DE PAGO
// ============================
Route::middleware(['auth'])->group(function () {

    // Pagos
    Route::get('/payments/{booking}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{booking}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/{booking}/success', [PaymentController::class, 'success'])->name('payments.success');

    // Tickets
    Route::get('/tickets/{booking}/download', [TicketController::class, 'download'])->name('payments.download-ticket');
    Route::get('/tickets/{booking}/view', [TicketController::class, 'view'])->name('tickets.view');
});

// ============================
// ADMIN ROUTES
// ============================
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Gestión de Películas
    Route::get('/movies', [App\Http\Controllers\Admin\MovieController::class, 'index'])->name('admin.movies.index');
    Route::get('/movies/create', [App\Http\Controllers\Admin\MovieController::class, 'create'])->name('admin.movies.create');
    Route::post('/movies', [App\Http\Controllers\Admin\MovieController::class, 'store'])->name('admin.movies.store');
    Route::get('/movies/{movie}/edit', [App\Http\Controllers\Admin\MovieController::class, 'edit'])->name('admin.movies.edit');
    Route::put('/movies/{movie}', [App\Http\Controllers\Admin\MovieController::class, 'update'])->name('admin.movies.update');
    Route::delete('/movies/{movie}', [App\Http\Controllers\Admin\MovieController::class, 'destroy'])->name('admin.movies.destroy');

    // Gestión de Salas
    Route::get('/rooms', [App\Http\Controllers\Admin\RoomController::class, 'index'])->name('admin.rooms.index');
    Route::get('/rooms/create', [App\Http\Controllers\Admin\RoomController::class, 'create'])->name('admin.rooms.create');
    Route::post('/rooms', [App\Http\Controllers\Admin\RoomController::class, 'store'])->name('admin.rooms.store');
    Route::get('/rooms/{room}/edit', [App\Http\Controllers\Admin\RoomController::class, 'edit'])->name('admin.rooms.edit');
    Route::put('/rooms/{room}', [App\Http\Controllers\Admin\RoomController::class, 'update'])->name('admin.rooms.update');
    Route::delete('/rooms/{room}', [App\Http\Controllers\Admin\RoomController::class, 'destroy'])->name('admin.rooms.destroy');

    // Ruta AJAX: obtener horarios
    Route::get('/rooms/{room}/schedule', function (\App\Models\Room $room) {
        $showtimes = $room->showtimes()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->with('movie')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'showtimes' => $showtimes
        ]);
    })->name('admin.rooms.schedule');

    // Gestión de Funciones
    Route::get('/showtimes', [App\Http\Controllers\Admin\ShowtimeController::class, 'index'])->name('admin.showtimes.index');
    Route::get('/showtimes/create', [App\Http\Controllers\Admin\ShowtimeController::class, 'create'])->name('admin.showtimes.create');
    Route::post('/showtimes', [App\Http\Controllers\Admin\ShowtimeController::class, 'store'])->name('admin.showtimes.store');
    Route::get('/showtimes/{showtime}/edit', [App\Http\Controllers\Admin\ShowtimeController::class, 'edit'])->name('admin.showtimes.edit');
    Route::put('/showtimes/{showtime}', [App\Http\Controllers\Admin\ShowtimeController::class, 'update'])->name('admin.showtimes.update');
    Route::delete('/showtimes/{showtime}', [App\Http\Controllers\Admin\ShowtimeController::class, 'destroy'])->name('admin.showtimes.destroy');

    // Gestión de Reservas
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('admin.bookings.show');
    Route::put('/bookings/{booking}/cancel', [App\Http\Controllers\Admin\BookingController::class, 'cancel'])->name('admin.bookings.cancel');
});

// ============================
// CASHIER ROUTES
// ============================
Route::prefix('cashier')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::get('/bookings/today', [CashierController::class, 'bookingsToday'])->name('cashier.bookings.today');

    // 🔧 Rutas de validación de reservas
    Route::get('/validate-booking', [CashierController::class, 'showValidationForm'])->name('cashier.validation.form');
    Route::post('/validate-booking', [CashierController::class, 'validateBooking'])->name('cashier.validate.booking');

    // Marcar reserva como usada
    Route::post('/booking/{booking}/mark-used', [CashierController::class, 'markAsUsed'])
        ->name('cashier.booking.mark-used');

    Route::get('/sales-report', [CashierController::class, 'salesReport'])->name('cashier.sales.report');
});

// ============================
// RUTA DE EMERGENCIA - PAGO EFECTIVO
// ============================
Route::get('/cash-payment/{booking}', function(Booking $booking) {
    if (!Auth::check() || !Auth::user()->isCashier()) {
        abort(403, 'Solo los cajeros pueden acceder.');
    }

    \Log::info('Pago de emergencia en efectivo - Booking: ' . $booking->id);

    $booking->update([
        'payment_status' => 'completed',
        'payment_method' => 'cash', 
        'payment_reference' => 'CASH-' . Str::random(6),
        'status' => 'confirmed'
    ]);

    return redirect()->route('payments.success', $booking)
        ->with('success', 'Pago en efectivo procesado exitosamente.');
})->name('emergency.cash.payment');
