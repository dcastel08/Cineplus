@extends('layouts.app')

@section('title', 'Validar Reserva - CinePlus')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode"></i> Validar Reserva</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cashier.validate.booking') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="booking_code" class="form-label">Código de Reserva</label>
                            <input type="text" class="form-control" id="booking_code" name="booking_code" 
                                   placeholder="Ingresa el código (Ej: CINEABC123)" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i> Validar Código
                            </button>
                        </div>
                    </form>

                    @if(session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection