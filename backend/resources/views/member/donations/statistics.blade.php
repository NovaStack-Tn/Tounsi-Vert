@extends('layouts.public')

@section('title', 'Donation Statistics')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-graph-up text-primary"></i> Donation Statistics</h2>
                    <p class="text-muted">View your donation analytics and impact</p>
                </div>
                <div>
                    <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Donations
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
                                <div class="ms-3">
                                    <h6 class="mb-0">Total Amount</h6>
                                    <h4 class="mb-0">{{ number_format($userStats['total_amount'], 2) }} TND</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                                <div class="ms-3">
                                    <h6 class="mb-0">Succeeded</h6>
                                    <h4 class="mb-0">{{ number_format($userStats['succeeded'], 2) }} TND</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock" style="font-size: 2.5rem;"></i>
                                <div class="ms-3">
                                    <h6 class="mb-0">Pending</h6>
                                    <h4 class="mb-0">{{ number_format($userStats['pending'], 2) }} TND</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-list-ol" style="font-size: 2.5rem;"></i>
                                <div class="ms-3">
                                    <h6 class="mb-0">Total Count</h6>
                                    <h4 class="mb-0">{{ $userStats['total_count'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Monthly Donations Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Monthly Donations ({{ date('Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyDonationsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Donations by Organization -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> By Organization</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="organizationChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations by Organization Table -->
            @if($donationsByOrganization->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Donations Breakdown by Organization</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Organization</th>
                                    <th class="text-center">Number of Donations</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donationsByOrganization as $item)
                                    @php
                                        $percentage = $userStats['total_amount'] > 0 
                                            ? ($item->total / $userStats['total_amount']) * 100 
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($item->organization)
                                                <a href="{{ route('organizations.show', $item->organization) }}" class="text-decoration-none">
                                                    <strong>{{ $item->organization->name }}</strong>
                                                </a>
                                                @if($item->organization->is_verified)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="bi bi-check-circle"></i> Verified
                                                    </span>
                                                @endif
                                            @else
                                                <em class="text-muted">N/A</em>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->count }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($item->total, 2) }} TND</strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $percentage }}%;" 
                                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-center">{{ $donationsByOrganization->sum('count') }}</td>
                                    <td class="text-end">{{ number_format($donationsByOrganization->sum('total'), 2) }} TND</td>
                                    <td class="text-center">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Donations -->
            @if($recentDonations->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Donations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Organization</th>
                                    <th>Event</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDonations as $donation)
                                    <tr>
                                        <td>{{ $donation->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($donation->organization)
                                                <a href="{{ route('organizations.show', $donation->organization) }}">
                                                    {{ $donation->organization->name }}
                                                </a>
                                            @else
                                                <em class="text-muted">N/A</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($donation->event)
                                                <a href="{{ route('events.show', $donation->event) }}">
                                                    {{ Str::limit($donation->event->title, 30) }}
                                                </a>
                                            @else
                                                <em class="text-muted">General</em>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($donation->amount, 2) }} TND</strong></td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'warning',
                                                    'succeeded' => 'success',
                                                    'failed' => 'danger',
                                                    'refunded' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusClasses[$donation->status] ?? 'secondary' }}">
                                                {{ ucfirst($donation->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('donations.show', $donation) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3">No donations yet</h5>
                        <p class="text-muted">Start making a difference by creating your first donation</p>
                        <a href="{{ route('donations.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Create Donation
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Monthly Donations Chart
    const monthlyCtx = document.getElementById('monthlyDonationsChart');
    const monthlyData = {!! json_encode($monthlyDonations) !!};
    
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthlyLabels = [];
    const monthlyValues = [];
    
    // Initialize all months with 0
    for (let i = 1; i <= 12; i++) {
        monthlyLabels.push(monthNames[i - 1]);
        const found = monthlyData.find(item => item.month === i);
        monthlyValues.push(found ? parseFloat(found.total) : 0);
    }
    
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Donations (TND)',
                data: monthlyValues,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' TND';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Amount: ' + context.parsed.y.toFixed(2) + ' TND';
                        }
                    }
                }
            }
        }
    });

    // Organization Pie Chart
    const orgCtx = document.getElementById('organizationChart');
    const orgData = {!! json_encode($donationsByOrganization) !!};
    
    const orgLabels = orgData.map(item => item.organization ? item.organization.name : 'N/A');
    const orgValues = orgData.map(item => parseFloat(item.total));
    
    const colors = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(255, 99, 255, 0.7)',
        'rgba(99, 255, 132, 0.7)'
    ];
    
    new Chart(orgCtx, {
        type: 'doughnut',
        data: {
            labels: orgLabels,
            datasets: [{
                data: orgValues,
                backgroundColor: colors.slice(0, orgLabels.length),
                borderColor: colors.slice(0, orgLabels.length).map(c => c.replace('0.7', '1')),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toFixed(2) + ' TND (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
