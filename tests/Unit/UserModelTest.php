<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserModelTest extends TestCase
{
    /** @test */
    public function test_user_model_uses_correct_traits()
    {
        $user = new User();
        
        // Verifica se o modelo usa os traits necessários
        $this->assertContains(HasFactory::class, class_uses($user));
        $this->assertContains(Notifiable::class, class_uses($user));
        $this->assertContains(HasApiTokens::class, class_uses($user));
        $this->assertContains(SoftDeletes::class, class_uses($user));
    }

    /** @test */
    public function test_user_model_extends_authenticatable()
    {
        $user = new User();
        
        // Verifica se o modelo estende Authenticatable
        $this->assertInstanceOf(Authenticatable::class, $user);
    }

    /** @test */
    public function test_user_model_has_correct_fillable_attributes()
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password'];
        
        // Verifica se os campos fillable estão corretos
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /** @test */
    public function test_user_model_has_correct_hidden_attributes()
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];
        
        // Verifica se os campos hidden estão corretos
        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /** @test */
    public function test_user_model_has_correct_casts()
    {
        $user = new User();
        $casts = $user->getCasts();
        
        // Verifica se os casts estão configurados corretamente
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function test_user_model_table_name()
    {
        $user = new User();
        
        // Verifica se o nome da tabela está correto (padrão do Laravel)
        $this->assertEquals('users', $user->getTable());
    }

    /** @test */
    public function test_user_model_primary_key()
    {
        $user = new User();
        
        // Verifica se a chave primária está correta
        $this->assertEquals('id', $user->getKeyName());
    }

    /** @test */
    public function test_user_model_uses_timestamps()
    {
        $user = new User();
        
        // Verifica se o modelo usa timestamps
        $this->assertTrue($user->usesTimestamps());
    }

    /** @test */
    public function test_user_model_can_be_instantiated()
    {
        $user = new User();
        
        // Verifica se o modelo pode ser instanciado
        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function test_user_model_has_factory()
    {
        // Verifica se o modelo tem factory configurada
        $this->assertTrue(method_exists(User::class, 'factory'));
    }

    /** @test */
    public function test_user_model_fillable_attributes_are_mass_assignable()
    {
        $user = new User();
        $attributes = [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'senha123'
        ];
        
        // Testa se os atributos fillable podem ser atribuídos em massa
        $user->fill($attributes);
        
        $this->assertEquals('João Silva', $user->name);
        $this->assertEquals('joao@email.com', $user->email);
        // O password é hasheado automaticamente, então verificamos se não está vazio
        $this->assertNotEmpty($user->password);
        $this->assertNotEquals('senha123', $user->password); // Deve ser diferente da senha original
    }

    /** @test */
    public function test_user_model_hidden_attributes_are_not_visible_in_array()
    {
        // Cria um usuário com dados de teste usando factory
        $user = User::factory()->make([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => 'senha123'
        ]);

        $userArray = $user->toArray();

        // Verifica se os campos ocultos não aparecem no array
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);

        // Verifica se os campos visíveis aparecem no array
        $this->assertArrayHasKey('name', $userArray);
        $this->assertArrayHasKey('email', $userArray);
    }

    /** @test */
    public function test_user_model_soft_deletes_configuration()
    {
        $user = new User();
        
        // Verifica se o soft delete está configurado
        $this->assertEquals('deleted_at', $user->getDeletedAtColumn());
    }

    /** @test */
    public function test_user_model_has_api_tokens_trait()
    {
        $user = new User();
        
        // Verifica se o modelo tem os métodos do HasApiTokens
        $this->assertTrue(method_exists($user, 'tokens'));
        $this->assertTrue(method_exists($user, 'createToken'));
        $this->assertTrue(method_exists($user, 'currentAccessToken'));
    }

    /** @test */
    public function test_user_model_has_notifiable_trait()
    {
        $user = new User();
        
        // Verifica se o modelo tem os métodos do Notifiable
        $this->assertTrue(method_exists($user, 'notify'));
        $this->assertTrue(method_exists($user, 'notifyNow'));
    }
}