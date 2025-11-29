<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'showtime_id',
        'ticket_count',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =============================
    // ESTADOS DE LA RESERVA
    // =============================
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_USED = 'used';

    // =============================
    // ESTADOS DE PAGO
    // =============================
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_COMPLETED = 'completed';
    const PAYMENT_FAILED = 'failed';

    // =============================
    // MÉTODOS DE PAGO
    // =============================
    const METHOD_CARD = 'card';
    const METHOD_CASH = 'cash';

    // =============================
    // RELACIONES
    // =============================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seat');
    }

    // =============================
    // BOOT METHOD
    // =============================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_code = 'CINE' . strtoupper(Str::random(8));
            $booking->status = self::STATUS_CONFIRMED;  // usar constante
            $booking->payment_status = self::PAYMENT_PENDING;
        });
    }

    // =============================
    // MÉTODOS DE PAGO
    // =============================
    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_COMPLETED;
    }

    public function isPendingPayment()
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }

    public function markAsPaid($method = self::METHOD_CARD, $reference = null)
    {
        $this->update([
            'payment_status' => self::PAYMENT_COMPLETED,
            'payment_method' => $method,
            'payment_reference' => $reference ?? 'PAY-' . Str::random(10),
        ]);
    }

    public function getPaymentMethodText()
    {
        return match($this->payment_method) {
            self::METHOD_CARD => 'Tarjeta de Crédito/Débito',
            self::METHOD_CASH => 'Efectivo',
            default => 'No especificado'
        };
    }

    public function getPaymentStatusText()
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'Pendiente',
            self::PAYMENT_COMPLETED => 'Completado',
            self::PAYMENT_FAILED => 'Fallido',
            default => 'Desconocido'
        };
    }

    // =============================
    // MÉTODOS DE RESERVA
    // =============================
    public function isUsed()
    {
        return $this->status === self::STATUS_USED;
    }
}
