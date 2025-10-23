@extends('layouts.admin')

@section('title', 'Détection d\'Anomalies IA')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-exclamation-triangle text-warning me-2"></i>Détection d'Anomalies</h2>
            <p class="text-muted">Organisations avec comportements suspects détectés par l'IA</p>
        </div>
        <a href="{{ route('admin.ai.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour au Dashboard IA
        </a>
    </div>

    @if(count($results) > 0)
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <i class="bi bi-robot me-2"></i>
            <strong>{{ count($results) }} organisation(s)</strong> avec anomalies détectées
        </div>

        @foreach($results as $result)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $result['organization']->name }}</h5>
                        <small class="text-muted">{{ $result['organization']->city }}, {{ $result['organization']->region }}</small>
                    </div>
                    <div>
                        <span class="badge bg-{{ $result['analysis']['risk_level'] === 'critical' ? 'danger' : ($result['analysis']['risk_level'] === 'high' ? 'warning' : 'info') }} px-3 py-2">
                            Risque: {{ ucfirst($result['analysis']['risk_level']) }} ({{ $result['analysis']['risk_score'] }}/100)
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-danger mb-3">
                                <i class="bi bi-bug-fill me-2"></i>Anomalies Détectées ({{ $result['analysis']['anomaly_count'] }})
                            </h6>
                            
                            @foreach($result['analysis']['anomalies'] as $anomaly)
                                <div class="alert alert-{{ $anomaly['severity'] === 'high' ? 'danger' : 'warning' }} mb-2">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-circle-fill me-2 mt-1"></i>
                                        <div>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $anomaly['type'])) }}</strong>
                                            <p class="mb-0 small">{{ $anomaly['message'] }}</p>
                                        </div>
                                        <span class="badge bg-{{ $anomaly['severity'] === 'high' ? 'danger' : 'warning' }} ms-auto">
                                            {{ ucfirst($anomaly['severity']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">Recommandation IA</h6>
                                    <p class="mb-3">{{ $result['analysis']['recommendation'] }}</p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.organizations.show', $result['organization']->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Voir Organisation
                                        </a>
                                        <a href="{{ route('admin.ai.organization', $result['organization']->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-robot me-1"></i>Analyse Complète
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Aucune Anomalie Détectée</h4>
                <p class="text-muted">Toutes les organisations semblent fonctionner normalement</p>
            </div>
        </div>
    @endif
</div>
@endsection
