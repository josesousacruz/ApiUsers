<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // Isso garante que o banco de dados será resetado após cada teste

    /** @test */
    public function it_can_create_a_user()
    {
        // Autentica um usuário para acessar a rota protegida
        $authenticatedUser = User::factory()->create();
        Sanctum::actingAs($authenticatedUser);

        // Dados de entrada
        $data = [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'senha123',
        ];

        // Realiza o POST para criar o usuário
        $response = $this->postJson('/api/users', $data);

        // Verifica se o status é 201 (created)
        $response->assertStatus(201);

        // Verifica se a resposta contém os dados do usuário criado
        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Usuario criado com sucesso',
        ]);

        // Verifica se o usuário foi realmente criado no banco de dados
        $this->assertDatabaseHas('users', [
            'email' => 'joao@email.com',
        ]);
    }

    /** @test */
    public function it_can_list_users()
    {
        // Cria um usuário
        $user = User::factory()->create();
        
        // Autentica um usuário para acessar a rota protegida
        Sanctum::actingAs($user);

        // Realiza o GET para listar usuários
        $response = $this->getJson('/api/users');

        // Verifica se o status é 200 (OK)
        $response->assertStatus(200);

        // Verifica se o usuário aparece na resposta
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_can_show_user_by_id()
    {
        // Cria um usuário
        $user = User::factory()->create();
        
        // Autentica um usuário para acessar a rota protegida
        Sanctum::actingAs($user);

        // Realiza o GET para buscar o usuário pelo ID
        $response = $this->getJson("/api/users/{$user->id}");

        // Verifica se o status é 200 (OK)
        $response->assertStatus(200);

        // Verifica se o usuário aparece na resposta
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_returns_404_when_user_not_found()
    {
        // Autentica um usuário para acessar a rota protegida
        $authenticatedUser = User::factory()->create();
        Sanctum::actingAs($authenticatedUser);
        
        // Realiza o GET para buscar um usuário que não existe
        $response = $this->getJson('/api/users/99999');

        // Verifica se o status é 404 (Not Found)
        $response->assertStatus(404);

        // Verifica a mensagem de erro
        $response->assertJson([
            'success' => false,
            'message' => 'Usuário não encontrado',
        ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        // Cria um usuário
        $user = User::factory()->create();
        
        // Autentica um usuário para acessar a rota protegida
        Sanctum::actingAs($user);

        // Dados para atualizar o usuário
        $data = [
            'name' => 'João Silva Atualizado',
            'email' => 'novoemail@email.com',
            'password' => 'novaSenha123',
        ];

        // Realiza o PUT para atualizar o usuário
        $response = $this->putJson("/api/users/{$user->id}", $data);

        // Verifica se o status é 200 (OK)
        $response->assertStatus(200);

        // Verifica a mensagem de sucesso
        $response->assertJsonFragment([
            'message' => 'Usuario atualizado com sucesso',
        ]);

        // Verifica se os dados do usuário foram atualizados no banco de dados
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'novoemail@email.com',
        ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        // Cria um usuário
        $user = User::factory()->create();
        
        // Autentica um usuário para acessar a rota protegida
        Sanctum::actingAs($user);

        // Realiza o DELETE para excluir o usuário
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Verifica se o status é 200 (OK)
        $response->assertStatus(200);

        // Verifica a mensagem de sucesso
        $response->assertJsonFragment([
            'message' => "Usuario {$user->id} deletado com sucesso",
        ]);

        // Verifica se o usuário foi removido do banco de dados (soft delete)
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_login_user()
    {
        // Cria um usuário
        $user = User::factory()->create([
            'password' => Hash::make('senha123'),
        ]);

        // Dados para login
        $data = [
            'email' => $user->email,
            'password' => 'senha123',
        ];

        // Realiza o POST para login
        $response = $this->postJson('/api/user/login', $data);

        // Verifica se o status é 200 (OK)
        $response->assertStatus(200);

        // Verifica se a resposta contém o token
        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Login realizado com sucesso',
        ]);
    }

    /** @test */
    public function it_cannot_login_with_invalid_credentials()
    {
        // Dados para login com credenciais inválidas
        $data = [
            'email' => 'invalido@email.com',
            'password' => 'senhaerrada',
        ];

        // Realiza o POST para login
        $response = $this->postJson('/api/user/login', $data);

        // Verifica se o status é 401 (Unauthorized)
        $response->assertStatus(401);

        // Verifica se a mensagem de erro está presente
        $response->assertJson([
            'success' => false,
            'message' => 'Credenciais inválidas',
        ]);
    }

    /** @test */
    public function it_can_register_user_without_authentication()
    {
        // Dados de entrada para registro
        $data = [
            'name' => 'Novo Usuário',
            'email' => 'novo@email.com',
            'password' => 'senha123',
        ];

        // Realiza o POST para registrar o usuário (rota pública)
        $response = $this->postJson('/api/user/register', $data);

        // Verifica se o status é 201 (created)
        $response->assertStatus(201);

        // Verifica se a resposta contém os dados do usuário criado
        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Usuario criado com sucesso',
        ]);

        // Verifica se o usuário foi realmente criado no banco de dados
        $this->assertDatabaseHas('users', [
            'email' => 'novo@email.com',
        ]);
    }

    /** @test */
    public function it_returns_401_when_accessing_protected_routes_without_authentication()
    {
        // Tenta acessar rota protegida sem autenticação
        $response = $this->getJson('/api/users');

        // Verifica se o status é 401 (Unauthorized)
        $response->assertStatus(401);

        // Verifica a mensagem de erro
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
