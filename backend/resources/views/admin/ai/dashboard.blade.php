@extends('layouts.admin')

@section('title', 'Dashboard IA - Intelligence Artificielle')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-robot text-primary me-2"></i>Dashboard Intelligence Artificielle</h2>
            <p class="text-muted">Insights et analyses automatiques de la plateforme</p>
        </div>
        <div>
            <a href="{{ route('admin.ai.anomalies') }}" class="btn btn-warning me-2">
                <i class="bi bi-exclamation-triangle me-1"></i>Anomalies
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Santé de la Plateforme -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="text-white mb-0"><i class="bi bi-heart-pulse me-2"></i>Santé de la Plateforme</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="p-4 bg-light rounded">
                        <h1 class="display-4 mb-2">{{ $insights['platform_health']['score'] }}</h1>
                        <p class="text-muted mb-0">Score de Santé</p>
                        <span class="badge bg-{{ $insights['platform_health']['status'] === 'excellent' ? 'success' : ($insights['platform_health']['status'] === 'good' ? 'info' : 'warning') }} mt-2">
                            {{ ucfirst($insights['platform_health']['status']) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h4 class="text-primary">{{ $insights['platform_health']['active_users'] }}</h4>
                                <small class="text-muted">Utilisateurs Actifs (30j)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h4 class="text-success">{{ $insights['platform_health']['upcoming_events'] }}</h4>
                                <small class="text-muted">Événements à Venir</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h4 class="text-info">{{ $insights['platform_health']['recent_participations'] }}</h4>
                                <small class="text-muted">Participations (7j)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements Tendance -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up-arrow text-success me-2"></i>Événements Tendance</h5>
                </div>
                <div class="card-body">
                    @if(count($insights['trending_events']) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($insights['trending_events'] as $index => $event)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-danger me-2">{{ $index + 1 }}</span>
                                        <strong>{{ $event['title'] }}</strong>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">
                                            <i class="bi bi-people-fill me-1"></i>{{ $event['participations'] }}
                                        </span>
                                        <span class="badge bg-info ms-1">
                                            <i class="bi bi-arrow-up"></i>{{ $event['trend'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">Aucun événement tendance pour le moment</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Organisations -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-trophy text-warning me-2"></i>Top Organisations</h5>
                </div>
                <div class="card-body">
                    @if(count($insights['top_organizations']) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($insights['top_organizations'] as $index => $org)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">
                                            @if($org['level'] === 'platinum') 🏆
                                            @elseif($org['level'] === 'gold') 🥇
                                            @elseif($org['level'] === 'silver') 🥈
                                            @else 🥉
                                            @endif
                                        </span>
                                        <strong>{{ $org['name'] }}</strong>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary">Score: {{ $org['quality_score'] }}</span>
                                        <span class="badge bg-{{ $org['level'] === 'platinum' ? 'dark' : ($org['level'] === 'gold' ? 'warning' : 'secondary') }} ms-1">
                                            {{ ucfirst($org['level']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">Aucune organisation pour le moment</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Engagement Utilisateur -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-people text-info me-2"></i>Engagement Utilisateur</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Taux de Participation</span>
                            <strong>{{ $insights['user_engagement']['participation_rate'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: {{ $insights['user_engagement']['participation_rate'] }}%">
                                {{ $insights['user_engagement']['participation_rate'] }}%
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Taux d'Avis</span>
                            <strong>{{ $insights['user_engagement']['review_rate'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" style="width: {{ $insights['user_engagement']['review_rate'] }}%">
                                {{ $insights['user_engagement']['review_rate'] }}%
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-{{ $insights['user_engagement']['engagement_level'] === 'high' ? 'success' : 'info' }} mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Niveau d'engagement: <strong>{{ ucfirst($insights['user_engagement']['engagement_level']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prédictions -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-crystal-ball text-purple me-2"></i>Prédictions IA</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="display-3 text-primary mb-2">{{ $insights['predictions']['next_month_events'] }}</h1>
                        <p class="text-muted">Événements prévus le mois prochain</p>
                        <span class="badge bg-{{ $insights['predictions']['trend'] === 'increasing' ? 'success' : 'warning' }} px-3 py-2">
                            <i class="bi bi-arrow-{{ $insights['predictions']['trend'] === 'increasing' ? 'up' : 'down' }} me-1"></i>
                            {{ ucfirst($insights['predictions']['trend']) }}
                        </span>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Confiance:</strong> {{ $insights['predictions']['confidence'] }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if(count($insights['alerts']) > 0)
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bell text-danger me-2"></i>Alertes IA</h5>
                    </div>
                    <div class="card-body">
                        @foreach($insights['alerts'] as $alert)
                            <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ $alert['message'] }}</strong>
                                    <p class="mb-0 small">Action recommandée: {{ $alert['action'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
