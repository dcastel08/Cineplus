@extends('layouts.app')

@section('title', 'Seleccionar Butacas - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2>Seleccionar Butacas</h2>
            
            <!-- Información de la función -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ $showtime->movie->poster_url ?? 'https://via.placeholder.com/200x300?text=No+Image' }}" 
                                 alt="{{ $showtime->movie->title }}" class="img-fluid rounded">
                        </div>
                        <div class="col-md-9">
                            <h4>{{ $showtime->movie->title }}</h4>
                            <p><strong>Sala:</strong> {{ $showtime->room->name }}</p>
                            <p><strong>Fecha y Hora:</strong> {{ $showtime->start_time->format('d/m/Y H:i') }}</p>
                            <p><strong>Precio por ticket:</strong> ${{ number_format($showtime->price, 2) }}</p>
                            <p><strong>Duración:</strong> {{ $showtime->movie->duration }} minutos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa de butacas -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selecciona tus butacas</h5>
                    
                    <!-- Leyenda -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="seat available me-2"></div>
                                    <small>Disponible</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="seat selected me-2"></div>
                                    <small>Seleccionado</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="seat occupied me-2"></div>
                                    <small>Ocupado</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="seat vip me-2"></div>
                                    <small>VIP</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="seat disabled me-2"></div>
                                    <small>No disponible</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pantalla del cine -->
                    <div class="text-center mb-4">
                        <div class="bg-dark text-light py-2 rounded">
                            <strong>PANTALLA</strong>
                        </div>
                    </div>

                    <!-- Butacas -->
                    <form id="bookingForm" action="{{ route('bookings.store', $showtime) }}" method="POST">
                        @csrf
                        <div class="d-flex flex-column align-items-center">
                            @php
                                $currentRow = null;
                            @endphp
                            
                            @foreach($seats as $seat)
                                @if($currentRow !== $seat->row_number)
                                    @if($currentRow !== null)
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-center mb-2">
                                    @php $currentRow = $seat->row_number; @endphp
                                @endif
                                
                                @php
                                    $isBooked = $bookedSeats->contains('id', $seat->id);
                                    $seatClass = 'seat ';
                                    $seatClass .= $isBooked ? 'occupied' : 'available';
                                    $seatClass .= $seat->type === 'vip' ? ' vip' : '';
                                    $seatClass .= $seat->type === 'disabled' ? ' disabled' : '';
                                @endphp
                                
                                <div class="seat-container">
                                    @if(!$isBooked && $seat->type !== 'disabled')
                                        <input type="checkbox" 
                                               name="seats[]" 
                                               value="{{ $seat->id }}" 
                                               id="seat-{{ $seat->id }}" 
                                               class="d-none seat-checkbox"
                                               data-price="{{ $showtime->price }}">
                                        <label for="seat-{{ $seat->id }}" class="{{ $seatClass }}">
                                            {{ $seat->seat_code }}
                                        </label>
                                    @else
                                        <div class="{{ $seatClass }}">
                                            {{ $seat->seat_code }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <!-- Resumen de compra -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Resumen de Compra</h6>
                                        <div id="selectedSeats">
                                            <p class="text-muted">No hay butacas seleccionadas</p>
                                        </div>
                                        <div class="mt-3">
                                            <strong>Total: $<span id="totalAmount">0.00</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                        Confirmar Reserva
                                    </button>
                                    <a href="{{ route('movies.show', $showtime->movie) }}" class="btn btn-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatCheckboxes = document.querySelectorAll('.seat-checkbox');
    const selectedSeatsDiv = document.getElementById('selectedSeats');
    const totalAmountSpan = document.getElementById('totalAmount');
    const submitBtn = document.getElementById('submitBtn');
    
    let selectedSeats = [];
    let totalAmount = 0;

    seatCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const seatId = this.value;
            const seatLabel = this.nextElementSibling;
            const price = parseFloat(this.dataset.price);
            
            if (this.checked) {
                selectedSeats.push({
                    id: seatId,
                    code: seatLabel.textContent,
                    price: price
                });
                seatLabel.classList.add('selected');
                seatLabel.classList.remove('available');
            } else {
                selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                seatLabel.classList.remove('selected');
                seatLabel.classList.add('available');
            }
            
            updateSummary();
        });
    });

    function updateSummary() {
        totalAmount = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
        
        if (selectedSeats.length > 0) {
            let html = '<ul class="list-unstyled">';
            selectedSeats.forEach(seat => {
                html += `<li>${seat.code} - $${seat.price.toFixed(2)}</li>`;
            });
            html += '</ul>';
            selectedSeatsDiv.innerHTML = html;
            totalAmountSpan.textContent = totalAmount.toFixed(2);
            submitBtn.disabled = false;
        } else {
            selectedSeatsDiv.innerHTML = '<p class="text-muted">No hay butacas seleccionadas</p>';
            totalAmountSpan.textContent = '0.00';
            submitBtn.disabled = true;
        }
    }
});
</script>
@endpush
@endsection