<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_dashboard_for_authenticated_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_home_redirects_to_login_for_unauthenticated_users()
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }
}
