<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 font-sans">

    @include('components.sidebar')

    <main class="lg:ml-64 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">Reports</h1>
                    <p class="text-sm text-slate-500">Revenue, peak hours, and service analytics</p>
                </div>

                <form method="GET" action="{{ route('admin.reports') }}">
                    <select name="period" onchange="this.form.submit()" class="rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                        @foreach ($periodOptions as $key => $label)
                            <option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
                <div class="rounded-2xl bg-white p-5 shadow border">
                    <p class="text-sm text-slate-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-slate-800">₱{{ number_format($totalRevenue, 2) }}</p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow border">
                    <p class="text-sm text-slate-500">Total Appointments</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalAppointments }}</p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow border">
                    <p class="text-sm text-slate-500">Top Peak Hour</p>
                    <p class="text-2xl font-bold text-slate-800">
                        {{ $peakHours->sortByDesc('total')->first()->hour ?? 'N/A' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow border">
                    <p class="text-sm text-slate-500">Report Period</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $periodLabel }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2 mb-6">
                <div class="rounded-2xl bg-white p-5 shadow border">
                    <h2 class="text-lg font-semibold mb-4">Revenue Trend</h2>
                    <canvas id="revenueTrendChart" height="120"></canvas>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow border">
                    <h2 class="text-lg font-semibold mb-4">Service Distribution</h2>
                    <canvas id="serviceDistributionChart" height="120"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow border mb-6">
                <h2 class="text-lg font-semibold mb-4">Service Breakdown</h2>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($serviceDistribution as $service)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="font-semibold text-slate-800">{{ $service->service_name }}</div>
                            <div class="text-sm text-slate-500 mt-1">{{ $service->description }}</div>
                            <div class="text-sm text-slate-500 mt-1">{{ $service->duration_minutes }} Minutes</div>
                            <div class="mt-3 text-sm text-slate-700">
                                <span class="font-medium">Bookings:</span> {{ $service->total }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow border">
                <h2 class="text-lg font-semibold mb-4">Peak Hours</h2>
                <canvas id="peakHoursChart" height="100"></canvas>
            </div>
        </div>
    </main>

    <script>
        const revenueTrend = @json($revenueTrend);
        const peakHours = @json($peakHours);
        const serviceDistribution = @json($serviceDistribution);

        new Chart(document.getElementById('revenueTrendChart'), {
            type: 'line',
            data: {
                labels: revenueTrend.labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueTrend.values,
                    borderColor: '#849753',
                    backgroundColor: 'rgba(132, 151, 83, 0.15)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        new Chart(document.getElementById('serviceDistributionChart'), {
            type: 'pie',
            data: {
                labels: serviceDistribution.map(item => item.service_name + ' - ' + item.description + ' - ' + item.duration_minutes + ' Minutes '),
                datasets: [{
                    data: serviceDistribution.map(item => item.total),
                    backgroundColor: ['#849753', '#6F4E37', '#60A5FA', '#F59E0B', '#EF4444', '#10B981', '#8B5CF6']
                }]
            },
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('peakHoursChart'), {
            type: 'bar',
            data: {
                labels: peakHours.map(item => item.hour),
                datasets: [{
                    label: 'Bookings',
                    data: peakHours.map(item => item.total),
                    backgroundColor: '#6F4E37'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>