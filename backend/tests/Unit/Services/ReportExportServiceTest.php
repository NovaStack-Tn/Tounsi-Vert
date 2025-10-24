<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ReportExportService;
use Illuminate\Support\Collection;

class ReportExportServiceTest extends TestCase
{
    public function test_export_to_csv_returns_valid_format()
    {
        $service = new ReportExportService();
        $reports = new Collection(); // Empty collection for testing
        
        $result = $service->exportToCSV($reports);

        $this->assertIsString($result);
        $this->assertStringContainsString('ID', $result);
        $this->assertStringContainsString('Date', $result);
        $this->assertStringContainsString('Status', $result);
    }

    public function test_export_to_json_returns_valid_json()
    {
        $service = new ReportExportService();
        $reports = new Collection();
        
        $result = $service->exportToJSON($reports);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('export_date', $decoded);
        $this->assertArrayHasKey('total_reports', $decoded);
        $this->assertEquals(0, $decoded['total_reports']);
    }

    public function test_generate_statistics_summary_returns_array()
    {
        $service = new ReportExportService();
        $reports = new Collection();
        
        $result = $service->generateStatisticsSummary($reports);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('by_status', $result);
        $this->assertArrayHasKey('by_priority', $result);
        $this->assertArrayHasKey('ai_stats', $result);
    }
}
class ReportExportServiceMoreTest extends TestCase
{
    public function test_csv_handles_empty_dataset()
    {
        $svc = new ReportExportService();
        $csv = $svc->exportToCsv([]); // implement to return header-only or empty
        $this->assertIsString($csv);
    }

    public function test_json_respects_filters()
    {
        $svc = new ReportExportService();
        $json = $svc->exportToJson(['from' => '2025-01-01', 'to' => '2025-12-31']);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('filters', $data);
    }

    public function test_summary_handles_large_numbers()
    {
        $svc = new ReportExportService();
        $stats = $svc->generateStatisticsSummary(collect(range(1, 1000)));
        $this->assertArrayHasKey('count', $stats);
        $this->assertSame(1000, $stats['count']);
    }
}
