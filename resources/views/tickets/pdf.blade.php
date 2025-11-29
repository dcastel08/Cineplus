<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $booking->booking_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .ticket { border: 2px solid #333; padding: 20px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #e74c3c; margin: 0; }
        .header .subtitle { color: #666; margin: 5px 0; }
        .section { margin-bottom: 15px; }
        .section-title { background: #f8f9fa; padding: 5px 10px; margin-bottom: 5px; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .qr-code { text-align: center; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc; color: #666; font-size: 12px; }
        .barcode { font-family: 'Libre Barcode 128', cursive; font-size: 24px; text-align: center; margin: 10px 0; }
        .important { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Encabezado -->
        <div class="header">
            <h1>CINEPLUS</h1>
            <div class="subtitle">Tu cine de confianza</div>
            <div class="barcode">*{{ $booking->booking_code }}*</div>
        </div>

        <!-- Información de la Película -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DE LA PELÍCULA</div>
            <div class="info-grid">
                <div><strong>Película:</strong> {{ $booking->showtime->movie->title }}</div>
                <div><strong>Duración:</strong> {{ $booking->showtime->movie->duration }} min</div>
                <div><strong>Género:</strong> {{ $booking->showtime->movie->genre }}</div>
                <div><strong>Clasificación:</strong> A</div>
            </div>
        </div>

        <!-- Información de la Función -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DE LA FUNCIÓN</div>
            <div class="info-grid">
                <div><strong>Fecha:</strong> {{ $booking->showtime->start_time->format('d/m/Y') }}</div>
                <div><strong>Hora:</strong> {{ $booking->showtime->start_time->format('H:i') }}</div>
                <div><strong>Sala:</strong> {{ $booking->showtime->room->name }}</div>
                <div><strong>Butacas:</strong> {{ $booking->seats->count() }}</div>
            </div>
        </div>

        <!-- Detalles de Butacas -->
        <div class="section">
            <div class="section-title">DETALLES DE BUTACAS</div>
            <div style="text-align: center;">
                @foreach($booking->seats as $seat)
                    <span style="display: inline-block; background: #e74c3c; color: white; padding: 5px 10px; margin: 2px; border-radius: 3px;">
                        {{ $seat->seat_code }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Información del Pago -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL PAGO</div>
            <div class="info-grid">
                <div><strong>Código de Reserva:</strong> {{ $booking->booking_code }}</div>
                <div><strong>Referencia de Pago:</strong> {{ $booking->payment_reference }}</div>
                <div><strong>Método de Pago:</strong> {{ $booking->getPaymentMethodText() }}</div>
                <div><strong>Total Pagado:</strong> ${{ number_format($booking->total_amount, 2) }}</div>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL CLIENTE</div>
            <div class="info-grid">
                <div><strong>Nombre:</strong> {{ $booking->user->name }}</div>
                <div><strong>Email:</strong> {{ $booking->user->email }}</div>
                <div><strong>Fecha de Reserva:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</div>
                <div><strong>Tickets:</strong> {{ $booking->ticket_count }}</div>
            </div>
        </div>

        <!-- Código QR (simulado) -->
        <div class="qr-code">
            <div style="border: 1px solid #ccc; padding: 10px; display: inline-block;">
                <div style="font-family: monospace; line-height: 1;">
                    ██████████████<br>
                    ██          ██<br>
                    ██  CINEPLUS  ██<br>
                    ██          ██<br>
                    ██████████████<br>
                </div>
                <div style="margin-top: 5px; font-size: 10px;">Código: {{ $booking->booking_code }}</div>
            </div>
        </div>

        <!-- Instrucciones Importantes -->
        <div class="important">
            <strong>INSTRUCCIONES IMPORTANTES:</strong><br>
            • Presenta este ticket al ingresar al cine<br>
            • Llega 30 minutos antes de la función<br>
            • Trae una identificación oficial<br>
            • El ticket es válido solo para la función indicada<br>
            • No se permiten cambios ni devoluciones
        </div>

        <!-- Pie de Página -->
        <div class="footer">
            <div>Gracias por elegir CinePlus</div>
            <div>www.cineplus.com | Tel: (555) 123-4567</div>
            <div>Ticket generado el: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>