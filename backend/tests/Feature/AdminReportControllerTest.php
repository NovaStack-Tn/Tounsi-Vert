<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminReportControllerTest extends TestCase
{
    public function test_admin_can_access_reports_page()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/reports');
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_reports_page()
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->get('/admin/reports');
        $response->assertStatus(403);
    }
}
