@extends('layouts.app')

@section('title', 'Procesar Pago - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Procesar Pago</h5>
                </div>
                <div class="card-body">
                    <!-- Mostrar errores -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Resumen de la Reserva -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Resumen de tu Reserva</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="{{ $booking->showtime->movie->poster_url ?? 'https://via.placeholder.com/100x150?text=No+Image' }}" 
                                         alt="{{ $booking->showtime->movie->title }}" 
                                         class="img-fluid rounded">
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $booking->showtime->movie->title }}</h5>
                                    <p class="mb-1"><strong>Sala:</strong> {{ $booking->showtime->room->name }}</p>
                                    <p class="mb-1"><strong>Fecha:</strong> {{ $booking->showtime->start_time->format('d/m/Y H:i') }}</p>
                                    <p class="mb-1"><strong>Butacas:</strong> 
                                        @foreach($booking->seats as $seat)
                                            {{ $seat->seat_code }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </p>
                                    <p class="mb-0"><strong>Total a pagar:</strong> 
                                        <span class="h5 text-success">${{ number_format($booking->total_amount, 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Pago -->
                    <form action="{{ route('payments.process', $booking) }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Campo oculto para el método de pago -->
                        <input type="hidden" name="payment_method" id="payment_method_input" value="{{ Auth::user()->isCashier() ? 'cash' : 'card' }}">

                        <!-- Métodos de Pago -->
                        <div class="mb-4">
                            <label class="form-label h6">Método de Pago</label>
                            <div class="row">
                                <!-- Tarjeta -->
                                <div class="col-md-{{ Auth::user()->isCashier() ? '6' : '12' }}">
                                    <div class="form-check card border-primary">
                                        <input class="form-check-input" type="radio" name="payment_method_ui" 
                                               id="card" value="card" {{ Auth::user()->isCashier() ? '' : 'checked' }}>
                                        <label class="form-check-label card-body text-center" for="card">
                                            <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                            <h6>Tarjeta de Crédito/Débito</h6>
                                            <small class="text-muted">Pago seguro en línea</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Efectivo - solo cajeros -->
                                @if(Auth::user()->isCashier())
                                <div class="col-md-6">
                                    <div class="form-check card">
                                        <input class="form-check-input" type="radio" name="payment_method_ui" 
                                               id="cash" value="cash" checked>
                                        <label class="form-check-label card-body text-center" for="cash">
                                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                            <h6>Pago en Efectivo</h6>
                                            <small class="text-muted">Pago en taquilla</small>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información de Efectivo - solo cajeros -->
                        @if(Auth::user()->isCashier())
                        <div id="cashInfo" class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Pago en Efectivo</h6>
                            <p class="mb-0">
                                El cliente pagará en efectivo en taquilla. Marca esta opción para ventas presenciales.
                            </p>
                        </div>
                        @endif

                        <!-- Formulario de Tarjeta -->
                        <div id="cardForm" style="{{ Auth::user()->isCashier() ? 'display: none;' : '' }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_holder" class="form-label">Nombre en la Tarjeta</label>
                                    <input type="text" class="form-control card-field" id="card_holder" name="card_holder" 
                                           placeholder="JUAN PEREZ" value="{{ old('card_holder') }}">
                                    @error('card_holder')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="card_number" class="form-label">Número de Tarjeta</label>
                                    <input type="text" class="form-control card-field" id="card_number" name="card_number" 
                                           placeholder="1234 5678 9012 3456" maxlength="19" value="{{ old('card_number') }}">
                                    @error('card_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="card_expiry" class="form-label">Fecha de Expiración</label>
                                    <input type="text" class="form-control card-field" id="card_expiry" name="card_expiry" 
                                           placeholder="MM/AA" maxlength="5" value="{{ old('card_expiry') }}">
                                    @error('card_expiry')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="card_cvc" class="form-label">CVC</label>
                                    <input type="text" class="form-control card-field" id="card_cvc" name="card_cvc" 
                                           placeholder="123" maxlength="3" value="{{ old('card_cvc') }}">
                                    @error('card_cvc')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tarjetas de Prueba</label>
                                    <small class="d-block text-muted">
                                        Usa: 4242 4242 4242 4242<br>
                                        CVC: 123<br>
                                        Exp: 12/30
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- BOTONES - siempre visibles -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                @if(Auth::user()->isCashier())
                                <i class="fas fa-money-bill-wave"></i> Confirmar Venta en Efectivo - ${{ number_format($booking->total_amount, 2) }}
                                @else
                                <i class="fas fa-lock"></i> Pagar ${{ number_format($booking->total_amount, 2) }}
                                @endif
                            </button>
                            <a href="{{ route('bookings.my-bookings') }}" class="btn btn-outline-secondary">
                                Cancelar y Volver a Mis Reservas
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const paymentMethodInput = document.getElementById('payment_method_input');
    const cardFields = document.querySelectorAll('.card-field');

    // ==============================
    // Toggle formulario tarjeta / efectivo
    // ==============================
    @if(Auth::user()->isCashier())
    const cardRadio = document.getElementById('card');
    const cashRadio = document.getElementById('cash');
    const cardForm = document.getElementById('cardForm');
    const cashInfo = document.getElementById('cashInfo');
    const submitBtn = document.getElementById('submitBtn');

    function togglePaymentForms() {
        if(cardRadio.checked){
            cardForm.style.display = 'block';
            cashInfo.style.display = 'none';
            paymentMethodInput.value = 'card';
            cardFields.forEach(f => f.setAttribute('required','required'));
            submitBtn.innerHTML = '<i class="fas fa-lock"></i> Procesar Pago con Tarjeta - ${{ number_format($booking->total_amount, 2) }}';
        } else {
            cardForm.style.display = 'none';
            cashInfo.style.display = 'block';
            paymentMethodInput.value = 'cash';
            cardFields.forEach(f => f.removeAttribute('required'));
            submitBtn.innerHTML = '<i class="fas fa-money-bill-wave"></i> Confirmar Venta en Efectivo - ${{ number_format($booking->total_amount, 2) }}';
        }
    }

    cardRadio.addEventListener('change', togglePaymentForms);
    cashRadio.addEventListener('change', togglePaymentForms);
    togglePaymentForms();
    @else
    cardFields.forEach(f => f.setAttribute('required','required'));
    paymentMethodInput.value = 'card';
    @endif

    // ==============================
    // Formateo de tarjeta
    // ==============================
    const cardNumberInput = document.getElementById('card_number');
    if(cardNumberInput) cardNumberInput.addEventListener('input', e => {
        let value = e.target.value.replace(/\D/g,'').substring(0,16);
        e.target.value = value.match(/.{1,4}/g)?.join(' ') ?? value;
    });

    const cardExpiryInput = document.getElementById('card_expiry');
    if(cardExpiryInput) cardExpiryInput.addEventListener('input', e=>{
        let value = e.target.value.replace(/\D/g,'').substring(0,4);
        e.target.value = value.length>=2 ? value.substring(0,2)+'/'+value.substring(2,4) : value;
    });

    const cardCvcInput = document.getElementById('card_cvc');
    if(cardCvcInput) cardCvcInput.addEventListener('input', e=>{
        e.target.value = e.target.value.replace(/\D/g,'').substring(0,3);
    });

    const cardHolderInput = document.getElementById('card_holder');
    if(cardHolderInput) cardHolderInput.addEventListener('input', e=>{
        e.target.value = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'');
    });

    // Botón para rellenar datos de prueba
    if(cardCvcInput){
        const testBtn = document.createElement('button');
        testBtn.type = 'button';
        testBtn.className = 'btn btn-outline-info btn-sm mt-2';
        testBtn.innerHTML = '<i class="fas fa-magic"></i> Rellenar Datos de Prueba';
        testBtn.addEventListener('click', ()=>{
            document.getElementById('card_holder').value='CLIENTE PRUEBA';
            document.getElementById('card_number').value='4242 4242 4242 4242';
            document.getElementById('card_expiry').value='12/30';
            document.getElementById('card_cvc').value='123';
        });
        cardCvcInput.closest('.mb-3').appendChild(testBtn);
    }

    // ==============================
    // Limpieza final y validación antes de enviar
    // ==============================
    paymentForm.addEventListener('submit', function(e){
        // Limpiar espacios del número de tarjeta
        if(cardNumberInput) cardNumberInput.value = cardNumberInput.value.replace(/\s/g,'');

        // Trim de otros campos
        if(cardHolderInput) cardHolderInput.value = cardHolderInput.value.trim();
        if(cardExpiryInput) cardExpiryInput.value = cardExpiryInput.value.trim();
        if(cardCvcInput) cardCvcInput.value = cardCvcInput.value.trim();

        console.log('Campos de tarjeta listos para enviar:', {
            cardHolder: cardHolderInput?.value,
            cardNumber: cardNumberInput?.value,
            cardExpiry: cardExpiryInput?.value,
            cardCvc: cardCvcInput?.value
        });
    });
});
</script>
@endpush
