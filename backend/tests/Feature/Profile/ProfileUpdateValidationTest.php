<?php

namespace Tests\Feature\Profile;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileUpdateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile_success()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'John Doe',            // required by validation
            'email' => 'john@example.com',   // required by validation
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'phone_number' => '555123456',
            'city' => 'Tunis',
        ];

        $response = $this->actingAs($user)
            ->patch('/profile', $payload);

        $response->assertRedirect(); // Breeze/Jetstream redirects back
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'city' => 'Tunis',
        ]);
    }

    public function test_update_profile_validation_errors()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch('/profile', ['name' => '']); // invalid field

        $response->assertStatus(302)
                 ->assertSessionHasErrors(['name']);
    }
}
