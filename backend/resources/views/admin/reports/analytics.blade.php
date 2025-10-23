@extends('layouts.admin')

@section('title', 'Reports Analytics & Statistics')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-graph-up text-primary me-2"></i>Reports Analytics & Statistics</h2>
            <p class="text-muted">Advanced insights and AI-powered analysis</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Reports
        </a>
    </div>

    <!-- Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center">
                    <i class="bi bi-flag-fill" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $analytics['overview']['total'] }}</h3>
                    <small>Total Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white text-center">
                    <i class="bi bi-clock-fill" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $analytics['overview']['open'] }}</h3>
                    <small>Open Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white text-center">
                    <i class="bi bi-gear-fill" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $analytics['overview']['in_review'] }}</h3>
                    <small>In Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white text-center">
                    <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $analytics['resolution_rate']['rate'] }}%</h3>
                    <small>Resolution Rate</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Priority Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Priority Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($analytics['by_priority'] as $priority => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-{{ ['critical' => 'dark', 'high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$priority] ?? 'secondary' }} me-2">
                                    {{ ucfirst($priority) }}
                                </span>
                                <div class="progress flex-grow-1 mx-3" style="height: 20px;">
                                    <div class="progress-bar bg-{{ ['critical' => 'dark', 'high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$priority] ?? 'secondary' }}" 
                                         style="width: {{ $analytics['overview']['total'] > 0 ? ($count / $analytics['overview']['total'] * 100) : 0 }}%">
                                    </div>
                                </div>
                                <strong>{{ $count }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Category Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($analytics['by_category'] as $category => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-capitalize">{{ $category }}</span>
                                <span class="badge bg-secondary">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up text-success me-2"></i>Trends</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $analytics['trends']['last_week'] }}</h4>
                                <small class="text-muted">Reports Last Week</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $analytics['trends']['last_month'] }}</h4>
                                <small class="text-muted">Reports Last Month</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $analytics['trends']['resolved_last_week'] }}</h4>
                                <small class="text-muted">Resolved Last Week</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Average Response Time:</strong> 
                        {{ $analytics['response_time']['average_days'] }} days 
                        ({{ $analytics['response_time']['average_hours'] }} hours)
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Reported Organizations -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-building text-danger me-2"></i>Top Reported Organizations</h5>
                </div>
                <div class="card-body">
                    @if(count($analytics['top_reported_organizations']) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($analytics['top_reported_organizations'] as $index => $org)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="badge bg-danger me-2">{{ $index + 1 }}</span>
                                        <strong>{{ $org['organization'] }}</strong>
                                    </div>
                                    <span class="badge bg-warning text-dark">{{ $org['report_count'] }} reports</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No organization reports yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resolution Statistics -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-check2-square text-success me-2"></i>Resolution Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-4 border rounded">
                                <h3 class="text-primary mb-2">{{ $analytics['resolution_rate']['total'] }}</h3>
                                <p class="text-muted mb-0">Total Reports</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 border rounded">
                                <h3 class="text-success mb-2">{{ $analytics['resolution_rate']['resolved'] }}</h3>
                                <p class="text-muted mb-0">Resolved</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 border rounded">
                                <h3 class="text-secondary mb-2">{{ $analytics['resolution_rate']['dismissed'] }}</h3>
                                <p class="text-muted mb-0">Dismissed</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 border rounded">
                                <h3 class="text-info mb-2">{{ $analytics['resolution_rate']['rate'] }}%</h3>
                                <p class="text-muted mb-0">Resolution Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Priority Chart
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
new Chart(priorityCtx, {
    type: 'doughnut',
    data: {
        labels: ['Critical', 'High', 'Medium', 'Low'],
        datasets: [{
            data: [
                {{ $analytics['by_priority']['critical'] ?? 0 }},
                {{ $analytics['by_priority']['high'] ?? 0 }},
                {{ $analytics['by_priority']['medium'] ?? 0 }},
                {{ $analytics['by_priority']['low'] ?? 0 }}
            ],
            backgroundColor: ['#343a40', '#dc3545', '#ffc107', '#17a2b8']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($analytics['by_category'])) !!},
        datasets: [{
            label: 'Reports',
            data: {!! json_encode(array_values($analytics['by_category'])) !!},
            backgroundColor: '#667eea'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
