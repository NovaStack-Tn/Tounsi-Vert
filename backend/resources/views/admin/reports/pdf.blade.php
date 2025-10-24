<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports Export - {{ $generatedAt->format('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #4CAF50;
            font-size: 24pt;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 10pt;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .stat-box h3 {
            font-size: 24pt;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        
        .stat-box p {
            font-size: 9pt;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-open { background: #fff3cd; color: #856404; }
        .badge-in-review { background: #cce5ff; color: #004085; }
        .badge-resolved { background: #d4edda; color: #155724; }
        .badge-dismissed { background: #f8d7da; color: #721c24; }
        
        .badge-critical { background: #721c24; color: white; }
        .badge-high { background: #dc3545; color: white; }
        .badge-medium { background: #ffc107; color: #333; }
        .badge-low { background: #17a2b8; color: white; }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .ai-indicator {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reports Export</h1>
        <p>Tounsi-Vert Platform | Generated: {{ $generatedAt->format('F d, Y - H:i:s') }}</p>
    </div>

    @if(!empty($stats))
    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $stats['total'] ?? $reports->count() }}</h3>
            <p>Total Reports</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['by_status']['open'] ?? 0 }}</h3>
            <p>Open</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['by_status']['resolved'] ?? 0 }}</h3>
            <p>Resolved</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['ai_stats']['gemini_analyzed'] ?? 0 }}</h3>
            <p>Gemini AI</p>
        </div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Priority</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 20%;">Reported Item</th>
                <th style="width: 15%;">Reporter</th>
                <th style="width: 10%;">AI Risk</th>
                <th style="width: 8%;">AI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ $report->created_at->format('Y-m-d') }}</td>
                <td>
                    <span class="badge badge-{{ $report->status }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $report->priority }}">
                        {{ ucfirst($report->priority) }}
                    </span>
                </td>
                <td>{{ $report->categoryLabel }}</td>
                <td>
                    @if($report->organization_id)
                        <strong>Org:</strong> {{ Str::limit($report->organization->name ?? 'N/A', 25) }}
                    @else
                        <strong>Event:</strong> {{ Str::limit($report->event->title ?? 'N/A', 25) }}
                    @endif
                </td>
                <td>{{ Str::limit($report->user->full_name ?? 'N/A', 20) }}</td>
                <td>
                    @if($report->ai_risk_score)
                        <strong>{{ $report->ai_risk_score }}</strong>/100
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if(isset($report->ai_analysis['ai_powered']) && $report->ai_analysis['ai_powered'])
                        <span class="ai-indicator">✓ Gemini</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($reports->count() > 20)
    <div class="page-break"></div>
    @endif

    <div class="footer">
        <p>Tounsi-Vert Platform - Environmental & Social Events Management</p>
        <p>This report is confidential and intended for administrative use only.</p>
        <p>Page generated on {{ $generatedAt->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
