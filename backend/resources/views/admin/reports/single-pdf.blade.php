<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report #{{ $report->id }} - {{ $generatedAt->format('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #dc3545;
            font-size: 28pt;
            margin-bottom: 10px;
        }
        
        .header .report-id {
            font-size: 14pt;
            color: #666;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        
        .section-title {
            font-size: 14pt;
            color: #4CAF50;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 8px;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px;
            color: #333;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .badge-open { background: #fff3cd; color: #856404; }
        .badge-in-review { background: #cce5ff; color: #004085; }
        .badge-resolved { background: #d4edda; color: #155724; }
        .badge-dismissed { background: #f8d7da; color: #721c24; }
        
        .badge-critical { background: #721c24; color: white; }
        .badge-high { background: #dc3545; color: white; }
        .badge-medium { background: #ffc107; color: #333; }
        .badge-low { background: #17a2b8; color: white; }
        
        .ai-section {
            background: #e7f3ff;
            border-left-color: #007bff;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .ai-section .section-title {
            color: #007bff;
        }
        
        .content-box {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .actions-timeline {
            margin-top: 15px;
        }
        
        .action-item {
            padding: 12px;
            margin-bottom: 10px;
            background: white;
            border-left: 3px solid #007bff;
            border-radius: 3px;
        }
        
        .action-header {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .action-date {
            font-size: 9pt;
            color: #999;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #999;
        }
        
        .alert {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚩 Report Details</h1>
        <p class="report-id">Report #{{ $report->id }}</p>
        <p style="font-size: 10pt; color: #999;">Generated: {{ $generatedAt->format('F d, Y - H:i:s') }}</p>
    </div>

    <!-- Status & Priority -->
    <div class="section">
        <div class="section-title">📋 Report Status</div>
        <div>
            <span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
            <span class="badge badge-{{ $report->priority }}">Priority: {{ ucfirst($report->priority) }}</span>
            <span class="badge" style="background: #6c757d; color: white;">{{ $report->categoryLabel }}</span>
            @if($report->ai_auto_flagged)
                <span class="badge" style="background: #dc3545; color: white;">🤖 AI Auto-Flagged</span>
            @endif
        </div>
    </div>

    <!-- AI Analysis -->
    @if($report->ai_analysis)
    <div class="ai-section">
        <div class="section-title">
            🤖 AI Analysis
            @if(isset($report->ai_analysis['ai_powered']) && $report->ai_analysis['ai_powered'])
                <span style="color: #28a745; font-size: 10pt;">(Powered by Gemini AI)</span>
            @endif
        </div>
        
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Suggested Category:</div>
                <div class="info-value"><strong>{{ ucfirst($report->ai_suggested_category ?? 'N/A') }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Confidence Score:</div>
                <div class="info-value">{{ $report->ai_confidence }}%</div>
            </div>
            <div class="info-row">
                <div class="info-label">Risk Score:</div>
                <div class="info-value"><strong>{{ $report->ai_risk_score }}/100</strong> ({{ ucfirst($report->aiRiskLevel) }})</div>
            </div>
        </div>
        
        @if(isset($report->ai_analysis['analysis_summary']) && !empty($report->ai_analysis['analysis_summary']))
        <div class="alert alert-info">
            <strong>💡 AI Analysis Summary:</strong><br>
            {{ $report->ai_analysis['analysis_summary'] }}
        </div>
        @endif
        
        @if(isset($report->ai_analysis['recommended_action']) && !empty($report->ai_analysis['recommended_action']))
        <div class="alert alert-warning">
            <strong>✅ Recommended Action:</strong><br>
            {{ $report->ai_analysis['recommended_action'] }}
        </div>
        @endif
        
        @if($report->ai_analysis['requires_immediate_attention'] ?? false)
        <div class="alert alert-danger">
            <strong>⚠️ URGENT:</strong> This report requires immediate attention!
        </div>
        @endif
    </div>
    @endif

    <!-- Reported Item -->
    <div class="section">
        <div class="section-title">📍 Reported Item</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type:</div>
                <div class="info-value">{{ $report->organization_id ? 'Organization' : 'Event' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">
                    @if($report->organization_id)
                        <strong>{{ $report->organization->name ?? 'N/A' }}</strong>
                    @else
                        <strong>{{ $report->event->title ?? 'N/A' }}</strong>
                    @endif
                </div>
            </div>
            @if($report->organization_id && $report->organization)
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $report->organization->city }}, {{ $report->organization->region }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reporter Information -->
    <div class="section">
        <div class="section-title">👤 Reporter Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $report->user->full_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $report->user->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Member Since:</div>
                <div class="info-value">{{ $report->user->created_at->format('F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="section">
        <div class="section-title">⚠️ Report Content</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Submitted:</div>
                <div class="info-value">{{ $report->created_at->format('F d, Y H:i:s') }}</div>
            </div>
        </div>
        
        <div class="content-box">
            <strong>Reason:</strong><br>
            {{ $report->reason }}
        </div>
        
        @if($report->details)
        <div class="content-box">
            <strong>Details:</strong><br>
            {{ $report->details }}
        </div>
        @endif
    </div>

    <!-- Actions History -->
    @if($report->actions->count() > 0)
    <div class="section">
        <div class="section-title">📝 Actions History ({{ $report->actions->count() }})</div>
        <div class="actions-timeline">
            @foreach($report->actions as $action)
            <div class="action-item">
                <div class="action-header">
                    {{ $action->actionTypeLabel }} - {{ $action->admin->full_name }}
                </div>
                <div class="action-date">
                    {{ $action->action_taken_at->format('F d, Y H:i:s') }}
                </div>
                @if($action->action_note)
                <div style="margin-top: 8px; padding: 8px; background: #f0f0f0; border-radius: 3px;">
                    <strong>Note:</strong> {{ $action->action_note }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Resolution -->
    @if($report->resolved_at)
    <div class="section" style="border-left-color: #28a745;">
        <div class="section-title" style="color: #28a745;">✅ Resolution</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Resolved At:</div>
                <div class="info-value">{{ $report->resolved_at->format('F d, Y H:i:s') }}</div>
            </div>
            @if($report->resolver)
            <div class="info-row">
                <div class="info-label">Resolved By:</div>
                <div class="info-value">{{ $report->resolver->full_name }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Resolution Time:</div>
                <div class="info-value">{{ $report->created_at->diffForHumans($report->resolved_at, true) }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p><strong>Tounsi-Vert Platform</strong> - Environmental & Social Events Management</p>
        <p>This report is confidential and intended for administrative use only.</p>
        <p>Document generated on {{ $generatedAt->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
