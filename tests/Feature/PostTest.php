<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        return [
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    public function test_can_list_posts()
    {
        Post::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'created_at', 'updated_at'],
                ],
                'message',
                'errors',
            ])
            ->assertJson([
                'message' => 'Posts listados com sucesso',
                'errors' => null,
            ]);
    }

    public function test_authenticated_user_can_create_post()
    {
        $payload = [
            'title' => 'Meu Post',
            'content' => 'Conteúdo do post',
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/posts', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Post criado com sucesso',
                'errors' => null,
            ])
            ->assertJsonStructure([
                'data' => ['id', 'title', 'content'],
            ]);

        $this->assertDatabaseHas('posts', $payload);
    }

    public function test_cannot_create_post_without_required_fields()
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/posts', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['title', 'content'],
            ]);
    }

    public function test_can_show_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders($this->authHeaders())
            ->getJson("/api/posts/{$post->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Post encontrado com sucesso',
                'errors' => null,
            ])
            ->assertJsonStructure([
                'data' => ['id', 'title', 'content'],
            ]);
    }

    public function test_authenticated_user_can_update_post()
    {
        $post = Post::factory()->create();

        $payload = [
            'title' => 'Título atualizado',
            'content' => 'Conteúdo atualizado',
        ];

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/posts/{$post->id}", $payload);

        $response->assertOk()
            ->assertJson([
                'message' => 'Post atualizado com sucesso',
            ]);

        $this->assertDatabaseHas('posts', $payload);
    }

    public function test_authenticated_user_can_delete_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Post deletado com sucesso',
                'errors' => null,
            ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_post()
    {
        $this->postJson('/api/posts', [
            'title' => 'X',
            'content' => 'Y',
        ])->assertStatus(401);
    }
}