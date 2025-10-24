@extends('layouts.admin')

@section('title', 'Advanced Reports Analytics')

@section('content')
<style>
    .metric-card {
        transition: all 0.3s ease;
        border-left: 4px solid #4CAF50;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-bar-chart-line text-primary me-2"></i>Advanced Reports Analytics</h2>
            <p class="text-muted">Comprehensive analysis and insights</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>Back to Reports
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Export Analytics
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.exportCSV', $filters) }}">
                        <i class="bi bi-filetype-csv me-2"></i>Export as CSV
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.exportJSON', $filters) }}">
                        <i class="bi bi-filetype-json me-2"></i>Export as JSON
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card metric-card border-0 shadow-sm" style="border-left-color: #667eea;">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-2">{{ $analytics['summary']['total'] }}</h3>
                    <p class="text-muted mb-0">Total Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card border-0 shadow-sm" style="border-left-color: #28a745;">
                <div class="card-body text-center">
                    <h3 class="text-success mb-2">{{ $analytics['resolution_metrics']['resolution_rate'] }}%</h3>
                    <p class="text-muted mb-0">Resolution Rate</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card border-0 shadow-sm" style="border-left-color: #ffc107;">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-2">{{ $analytics['resolution_metrics']['pending_reports'] }}</h3>
                    <p class="text-muted mb-0">Pending Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card border-0 shadow-sm" style="border-left-color: #17a2b8;">
                <div class="card-body text-center">
                    <h3 class="text-info mb-2">{{ $analytics['summary']['ai_stats']['gemini_analyzed'] }}</h3>
                    <p class="text-muted mb-0">Gemini AI Analyzed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Priority Breakdown -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Status </h5>
                </div>
                <div class="card-body">
            
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-warning me-2"></i>Open</span>
                            <strong>{{ $analytics['summary']['by_status']['open'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_status']['open'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_status']['open'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-primary me-2"></i>In Review</span>
                            <strong>{{ $analytics['summary']['by_status']['in_review'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-primary" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_status']['in_review'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_status']['in_review'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-success me-2"></i>Resolved</span>
                            <strong>{{ $analytics['summary']['by_status']['resolved'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_status']['resolved'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_status']['resolved'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-danger me-2"></i>Dismissed</span>
                            <strong>{{ $analytics['summary']['by_status']['dismissed'] }}</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_status']['dismissed'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_status']['dismissed'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Priority </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-dark me-2"></i>Critical</span>
                            <strong>{{ $analytics['summary']['by_priority']['critical'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-dark" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_priority']['critical'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_priority']['critical'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-danger me-2"></i>High</span>
                            <strong>{{ $analytics['summary']['by_priority']['high'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-danger" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_priority']['high'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_priority']['high'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-warning me-2"></i>Medium</span>
                            <strong>{{ $analytics['summary']['by_priority']['medium'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_priority']['medium'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_priority']['medium'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-info me-2"></i>Low</span>
                            <strong>{{ $analytics['summary']['by_priority']['low'] }}</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-info" style="width: {{ $analytics['summary']['total'] > 0 ? ($analytics['summary']['by_priority']['low'] / $analytics['summary']['total']) * 100 : 0 }}%">
                                {{ $analytics['summary']['total'] > 0 ? round(($analytics['summary']['by_priority']['low'] / $analytics['summary']['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  

    <!-- Top Reporters & Organizations -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Top Reporters</h5>
                </div>
                <div class="card-body">
                    @if($analytics['top_reporters']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th class="text-end">Reports</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['top_reporters'] as $index => $reporter)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $reporter['name'] }}</td>
                                        <td><small class="text-muted">{{ $reporter['email'] }}</small></td>
                                        <td class="text-end"><strong>{{ $reporter['count'] }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Most Reported Organizations</h5>
                </div>
                <div class="card-body">
                    @if($analytics['top_reported_organizations']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Organization</th>
                                        <th>Categories</th>
                                        <th class="text-end">Reports</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['top_reported_organizations'] as $index => $org)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $org['name'] }}</td>
                                        <td>
                                            @foreach($org['categories'] as $cat)
                                                <span class="badge bg-secondary">{{ $cat }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end"><strong class="text-danger">{{ $org['count'] }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resolution Metrics -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Resolution Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-success">{{ $analytics['resolution_metrics']['resolution_rate'] }}%</h3>
                            <p class="text-muted">Resolution Rate</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-primary">{{ $analytics['resolution_metrics']['average_resolution_time'] ?? 'N/A' }}</h3>
                            <p class="text-muted">Average Resolution Time</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-warning">{{ $analytics['resolution_metrics']['pending_reports'] }}</h3>
                            <p class="text-muted">Pending Reports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
