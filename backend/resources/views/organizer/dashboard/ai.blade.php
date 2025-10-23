@extends('layouts.organizer')

@section('title', 'AI Insights')
@section('page-title', '🤖 Intelligence Artificielle')
@section('page-subtitle', 'Analyses et recommandations pilotées par l\'IA')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('organizer.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">Total Collecté (90j)</h6>
                    <h3 class="mb-0">{{ number_format($totalAmount, 2) }} TND</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">Nombre de Dons</h6>
                    <h3 class="mb-0">{{ $donationCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">Don Moyen</h6>
                    <h3 class="mb-0">{{ number_format($averageDonation, 2) }} TND</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Card 1: AI Donation Insights -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow rounded border-0 h-100">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        🤖 AI Donation Insights
                    </h5>
                </div>
                <div class="card-body bg-light">
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Analyse automatique des tendances de dons sur 90 jours</small>
                    </div>
                    
                    <div class="p-3 bg-white rounded border" style="min-height: 150px;">
                        <p class="mb-0 text-dark" style="line-height: 1.8;">
                            {{ $aiInsights }}
                        </p>
                    </div>

                    @if($donationCount > 0)
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-graph-up me-1"></i>
                            Basé sur {{ $donationCount }} don(s) • {{ number_format($totalAmount, 2) }} TND
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Next-Best Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow rounded border-0 h-100">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-bullseye text-danger me-2"></i>
                        🎯 Next-Best Actions
                    </h5>
                </div>
                <div class="card-body bg-light">
                    <div class="alert alert-success border-0 shadow-sm">
                        <i class="bi bi-check-circle me-2"></i>
                        <small>Recommandations pour améliorer vos campagnes</small>
                    </div>

                    <div class="p-3 bg-white rounded border">
                        <ul class="mb-0" style="line-height: 2;">
                            @foreach($nextBestActions as $action)
                                <li class="mb-2">
                                    <strong class="text-primary">→</strong> {{ $action }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Régénérer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Thank-You Template -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow rounded border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-heart-fill text-success me-2"></i>
                        💚 Thank-You Template
                    </h5>
                </div>
                <div class="card-body bg-light">
                    <div class="alert alert-warning border-0 shadow-sm">
                        <i class="bi bi-envelope-heart me-2"></i>
                        <small>Message de remerciement personnalisé généré par IA</small>
                    </div>

                    <div class="position-relative">
                        <textarea 
                            id="thankYouTemplate" 
                            class="form-control bg-white border shadow-sm" 
                            rows="6" 
                            style="font-family: 'Georgia', serif; line-height: 1.8;"
                            readonly>{{ $thankYouTemplate }}</textarea>
                        
                        <button 
                            class="btn btn-success position-absolute top-0 end-0 m-2" 
                            onclick="copyThankYou()"
                            id="copyButton">
                            <i class="bi bi-clipboard"></i> Copier
                        </button>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Régénérer
                        </button>
                        <button class="btn btn-outline-info" onclick="editTemplate()">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 bg-gradient text-white shadow" style="background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);">
                <div class="card-body text-center py-4">
                    <h6 class="mb-2">
                        <i class="bi bi-shield-check me-2"></i>
                        Confidentialité & Sécurité
                    </h6>
                    <p class="mb-0 small opacity-75">
                        Aucune donnée personnelle de donateur n'est partagée avec l'IA. 
                        Seules les statistiques agrégées sont utilisées pour générer ces insights.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Copy thank-you template to clipboard
    function copyThankYou() {
        const textarea = document.getElementById('thankYouTemplate');
        const button = document.getElementById('copyButton');
        
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile devices
        
        navigator.clipboard.writeText(textarea.value).then(() => {
            // Success feedback
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-lg"></i> Copié!';
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
            }, 2000);
        }).catch(err => {
            alert('Erreur lors de la copie. Veuillez réessayer.');
        });
    }

    // Enable editing of template
    function editTemplate() {
        const textarea = document.getElementById('thankYouTemplate');
        const button = document.getElementById('copyButton');
        
        if (textarea.hasAttribute('readonly')) {
            textarea.removeAttribute('readonly');
            textarea.focus();
            textarea.classList.add('border-warning');
            button.innerHTML = '<i class="bi bi-lock-fill"></i> Verrouiller';
        } else {
            textarea.setAttribute('readonly', true);
            textarea.classList.remove('border-warning');
            button.innerHTML = '<i class="bi bi-clipboard"></i> Copier';
        }
    }

    // Auto-refresh option (optional)
    let refreshTimer = null;
    
    function enableAutoRefresh(minutes) {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
        
        refreshTimer = setInterval(() => {
            location.reload();
        }, minutes * 60 * 1000);
        
        console.log(`Auto-refresh enabled: every ${minutes} minute(s)`);
    }
    
    // Uncomment to enable auto-refresh every 30 minutes
    // enableAutoRefresh(30);
</script>
@endpush
@endsection
