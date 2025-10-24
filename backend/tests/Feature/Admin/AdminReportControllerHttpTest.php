<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminReportControllerHttpTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(string $role)
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_admin_reports_index_ok()
    {
        $admin = $this->actingAsRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('Reports'); // any keyword in your Blade
    }

    public function test_member_is_forbidden_on_reports()
    {
        $member = $this->actingAsRole('member');

        $this->actingAs($member)
            ->get('/admin/reports')
            ->assertStatus(403);
    }

    public function test_admin_can_export_csv()
    {
        $admin = $this->actingAsRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports/export?format=csv')
            ->assertOk() // should return 200 now
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_invalid_export_format_redirects_with_validation_error()
    {
        $admin = $this->actingAsRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports/export?format=weird')
            ->assertStatus(302) // Laravel validation redirect
            ->assertSessionHasErrors(['format']);
    }
}
