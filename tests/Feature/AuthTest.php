<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Console\View\Components\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertNoContent();
    }

    public function test_logout_requires_authentication()
    {
        $this->postJson('/api/logout')
            ->assertStatus(401);
    }
}