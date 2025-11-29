@extends('layouts.app')

@section('title', 'Reporte de Ventas - CinePlus')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Reporte de Ventas - {{ now()->format('d/m/Y') }}</h5>
                    <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <!-- Resumen General -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body">
                                    <h6>Ventas Totales</h6>
                                    <h3>${{ number_format($salesData->total_sales ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body">
                                    <h6>Tickets Vendidos</h6>
                                    <h3>{{ $salesData->total_tickets ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body">
                                    <h6>Total Reservas</h6>
                                    <h3>{{ $salesData->total_bookings ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body">
                                    <h6>Ticket Promedio</h6>
                                    <h3>${{ number_format($salesData->average_sale ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(($salesData->total_bookings ?? 0) > 0)
                    <div class="row">
                        <!-- Ventas por Película -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h6 class="mb-0">Ventas por Película</h6></div>
                                <div class="card-body">
                                    @if($movieSales->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Película</th>
                                                    <th>Tickets</th>
                                                    <th>Ventas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($movieSales as $movie)
                                                <tr>
                                                    <td>{{ $movie->title }}</td>
                                                    <td><span class="badge bg-info">{{ $movie->tickets }}</span></td>
                                                    <td><strong>${{ number_format($movie->sales, 2) }}</strong></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p class="text-muted">No hay datos de ventas por película.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Ventas por Hora -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h6 class="mb-0">Ventas por Hora</h6></div>
                                <div class="card-body">
                                    @if($hourlySales->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Reservas</th>
                                                    <th>Ventas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hourlySales as $hour)
                                                <tr>
                                                    <td>{{ $hour->hour }}:00</td>
                                                    <td>{{ $hour->bookings }}</td>
                                                    <td>${{ number_format($hour->sales, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p class="text-muted">No hay ventas por hora.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Ventas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header"><h6 class="mb-0">Distribución de Ventas por Hora</h6></div>
                                <div class="card-body">
                                    @if($hourlySales->count() > 0)
                                    <div style="height: 300px;">
                                        <canvas id="salesChart"></canvas>
                                    </div>
                                    @else
                                        <p class="text-muted">No hay datos para mostrar el gráfico.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5>No hay ventas hoy</h5>
                        <p class="text-muted">No se han registrado ventas para el día de hoy.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hourlySales = @json($hourlySales);

    if(hourlySales.length > 0){
        const ctx = document.getElementById('salesChart').getContext('2d');
        const hours = hourlySales.map(h => h.hour + ':00');
        const sales = hourlySales.map(h => parseFloat(h.sales));
        const bookings = hourlySales.map(h => parseInt(h.bookings));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [
                    {
                        label: 'Ventas ($)',
                        data: sales,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Reservas',
                        data: bookings,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Ventas ($)' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: { display: true, text: 'Reservas' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
