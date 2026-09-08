@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Reports')

@section('content')

    <div class="min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- ========================================================= --}}
            {{-- HEADER --}}
            {{-- ========================================================= --}}

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Reports
                    </h1>

                    <p class="text-gray-600 mt-1">
                        Revenue, peak hours, and service analytics
                    </p>
                </div>


                {{-- ===================================================== --}}
                {{-- PERIOD FILTER --}}
                {{-- ===================================================== --}}

                <form method="GET" action="{{ route('admin.reports') }}" class="flex items-center gap-2">

                    <label for="period" class="text-sm font-semibold text-gray-700">
                        Period:
                    </label>

                    <select id="period" name="period" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm focus:border-[#6F4E37] focus:ring-[#6F4E37]">

                        @foreach ($periodOptions as $value => $label)
                            <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>

                </form>

            </div>


            {{-- ========================================================= --}}
            {{-- STAT CARDS --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                {{-- Total Revenue --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                    <div class="text-center">

                        <div>
                            <p class="text-2xl font-bold text-gray-800 mt-2">
                                ₱{{ number_format($totalRevenue, 2) }}
                            </p>

                            <p class="text-sm font-medium text-gray-500">
                                Total Revenue
                            </p>

                            

                        </div>
                    </div>

                </div>


                {{-- Total Appointments --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                    <div class="text-center">

                        <div>

                            <p class="text-2xl font-bold text-gray-800 mt-2">
                                {{ number_format($totalAppointments) }}
                            </p>

                            <p class="text-sm font-medium text-gray-500">
                                Total Appointments
                            </p>

                            

                        </div>

                        

                    </div>

                </div>


                {{-- Peak Hour --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                    <div class="text-center">

                        <div>
                            <p class="text-2xl font-bold text-gray-800 mt-2">

                                {{ $peakHours->sortByDesc('total')->first()->hour ?? 'N/A' }}

                            </p>

                            <p class="text-sm font-medium text-gray-500">
                                Top Peak Hour
                            </p>

                            

                        </div>


                    </div>

                </div>


                {{-- Report Period --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">

                    <div class="text-center">

                        <div>
                            <p class="text-2xl font-bold text-gray-800 mt-2">
                                {{ $periodLabel }}
                            </p>

                            <p class="text-sm font-medium text-gray-500">
                                Report Period
                            </p>

                        </div>


                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- REVENUE TREND --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Revenue Trend
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Revenue generated during {{ $periodLabel }}
                    </p>

                </div>

                <div class="relative h-80">

                    <canvas id="revenueTrendChart"></canvas>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- SERVICE DISTRIBUTION --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                {{-- Pie Chart --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <div class="mb-6">

                        <h2 class="text-xl font-bold text-gray-800">
                            Service Distribution
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Appointment distribution by service
                        </p>

                    </div>

                    <div class="relative h-80">

                        <canvas id="serviceDistributionChart"></canvas>

                    </div>

                </div>


                {{-- Service Breakdown --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <div class="mb-6">

                        <h2 class="text-xl font-bold text-gray-800">
                            Service Breakdown
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Detailed service statistics
                        </p>

                    </div>


                    <div class="space-y-4">

                        @forelse($serviceDistribution as $service)
                            <div class="border border-gray-100 rounded-xl p-4">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">

                                        <h3 class="font-semibold text-gray-800">
                                            {{ $service->service_name }}
                                        </h3>

                                        @if ($service->description)
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $service->description }}
                                            </p>
                                        @endif

                                        <p class="text-xs text-gray-400 mt-2">

                                            Duration:
                                            {{ $service->duration_minutes }}
                                            minutes

                                        </p>

                                    </div>


                                    <div class="text-right shrink-0">

                                        <p class="text-xl font-bold text-gray-800">
                                            {{ number_format($service->total) }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Bookings
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-12 text-gray-500">

                                <p>
                                    No service data available for this period.
                                </p>

                            </div>
                        @endforelse

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PEAK HOURS --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Peak Hours
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Number of appointments by hour
                    </p>

                </div>

                <div class="relative h-80">

                    <canvas id="peakHoursChart"></canvas>

                </div>

            </div>

        </div>

    </div>

@endsection


{{-- ============================================================= --}}
{{-- CHART SCRIPTS --}}
{{-- ============================================================= --}}

@push('scripts')
    <script>
        (function() {

            let revenueChart = null;
            let serviceChart = null;
            let peakHoursChart = null;


            function initializeReportsCharts() {

                console.log('REPORTS SCRIPT INITIALIZING');


                /*
                |--------------------------------------------------------------------------
                | Check Chart.js
                |--------------------------------------------------------------------------
                */

                if (typeof window.Chart === 'undefined') {

                    console.error(
                        'Chart.js is not loaded.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Get Canvas Elements
                |--------------------------------------------------------------------------
                */

                const revenueCanvas =
                    document.getElementById(
                        'revenueTrendChart'
                    );

                const serviceCanvas =
                    document.getElementById(
                        'serviceDistributionChart'
                    );

                const peakCanvas =
                    document.getElementById(
                        'peakHoursChart'
                    );


                console.log(
                    'Revenue Canvas:',
                    revenueCanvas
                );

                console.log(
                    'Service Canvas:',
                    serviceCanvas
                );

                console.log(
                    'Peak Canvas:',
                    peakCanvas
                );


                /*
                |--------------------------------------------------------------------------
                | Stop If Canvases Don't Exist
                |--------------------------------------------------------------------------
                */

                if (
                    !revenueCanvas ||
                    !serviceCanvas ||
                    !peakCanvas
                ) {

                    console.error(
                        'One or more chart canvases were not found.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Destroy Existing Charts
                |--------------------------------------------------------------------------
                */

                if (revenueChart) {

                    revenueChart.destroy();

                    revenueChart = null;
                }


                if (serviceChart) {

                    serviceChart.destroy();

                    serviceChart = null;
                }


                if (peakHoursChart) {

                    peakHoursChart.destroy();

                    peakHoursChart = null;
                }


                /*
                |--------------------------------------------------------------------------
                | Laravel Data
                |--------------------------------------------------------------------------
                */

                const revenueTrend =
                    @json($revenueTrend);

                const peakHours =
                    @json($peakHours);

                const serviceDistribution =
                    @json($serviceDistribution);


                console.log(
                    'Revenue Trend:',
                    revenueTrend
                );

                console.log(
                    'Peak Hours:',
                    peakHours
                );

                console.log(
                    'Service Distribution:',
                    serviceDistribution
                );


                /*
                |--------------------------------------------------------------------------
                | REVENUE TREND CHART
                |--------------------------------------------------------------------------
                */

                revenueChart = new Chart(
                    revenueCanvas, {
                        type: 'line',

                        data: {

                            labels: revenueTrend.labels,

                            datasets: [

                                {

                                    label: 'Revenue',

                                    data: revenueTrend.values,

                                    borderColor: '#849753',

                                    backgroundColor: 'rgba(132, 151, 83, 0.15)',

                                    borderWidth: 3,

                                    pointRadius: 4,

                                    pointHoverRadius: 6,

                                    tension: 0.3,

                                    fill: true

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            interaction: {

                                intersect: false,

                                mode: 'index'

                            },

                            plugins: {

                                legend: {

                                    display: true

                                },

                                tooltip: {

                                    callbacks: {

                                        label: function(context) {

                                            const value =
                                                Number(
                                                    context.raw || 0
                                                );

                                            return '₱' +
                                                value.toLocaleString(
                                                    'en-PH', {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                );

                                        }

                                    }

                                }

                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        callback: function(value) {

                                            return '₱' +
                                                Number(value)
                                                .toLocaleString(
                                                    'en-PH'
                                                );

                                        }

                                    }

                                }

                            }

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SERVICE DISTRIBUTION CHART
                |--------------------------------------------------------------------------
                */

                serviceChart = new Chart(
                    serviceCanvas, {
                        type: 'pie',

                        data: {

                            labels: serviceDistribution.map(
                                item => {

                                    return (
                                        item.service_name +
                                        ' - ' +
                                        item.description +
                                        ' - ' +
                                        item.duration_minutes +
                                        ' Minutes'
                                    );

                                }
                            ),

                            datasets: [

                                {

                                    data: serviceDistribution.map(
                                        item => item.total
                                    ),

                                    backgroundColor: [

                                        '#849753',

                                        '#6F4E37',

                                        '#60A5FA',

                                        '#F59E0B',

                                        '#EF4444',

                                        '#10B981',

                                        '#8B5CF6',

                                        '#EC4899',

                                        '#14B8A6',

                                        '#6366F1'

                                    ],

                                    borderWidth: 2,

                                    borderColor: '#ffffff'

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    position: 'bottom'

                                },

                                tooltip: {

                                    callbacks: {

                                        label: function(context) {

                                            const value =
                                                Number(
                                                    context.raw || 0
                                                );

                                            const total =
                                                context.dataset.data
                                                .reduce(
                                                    (
                                                        sum,
                                                        current
                                                    ) =>
                                                    sum +
                                                    Number(current),
                                                    0
                                                );

                                            const percentage =
                                                total > 0 ?
                                                (
                                                    value /
                                                    total *
                                                    100
                                                ).toFixed(1) :
                                                0;

                                            return (
                                                context.label +
                                                ': ' +
                                                value +
                                                ' (' +
                                                percentage +
                                                '%)'
                                            );

                                        }

                                    }

                                }

                            }

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | PEAK HOURS CHART
                |--------------------------------------------------------------------------
                */

                peakHoursChart = new Chart(
                    peakCanvas, {
                        type: 'bar',

                        data: {

                            labels: peakHours.map(
                                item => item.hour
                            ),

                            datasets: [

                                {

                                    label: 'Bookings',

                                    data: peakHours.map(
                                        item => item.total
                                    ),

                                    backgroundColor: '#6F4E37',

                                    borderRadius: 6,

                                    borderSkipped: false

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    display: true

                                }

                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        precision: 0

                                    }

                                }

                            }

                        }

                    }
                );


                console.log(
                    'REPORTS CHARTS INITIALIZED SUCCESSFULLY'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Normal Browser Load
            |--------------------------------------------------------------------------
            */

            if (
                document.readyState === 'loading'
            ) {

                document.addEventListener(
                    'DOMContentLoaded',
                    initializeReportsCharts
                );

            } else {

                initializeReportsCharts();

            }


            document.addEventListener(
                'livewire:navigated',
                function() {

                    initializeReportsCharts();

                }
            );

        })();
    </script>
@endpush
