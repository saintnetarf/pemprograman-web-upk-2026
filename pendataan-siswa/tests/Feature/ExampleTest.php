<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_redirects_guest_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Operator Sekolah',
            'email' => 'sekolah@sekolah.test',
            'password' => 'password',
            'role' => Role::Sekolah,
        ])->save();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
