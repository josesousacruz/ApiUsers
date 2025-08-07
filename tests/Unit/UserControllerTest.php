<?php

namespace Tests\Unit;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Exception;

class UserControllerTest extends TestCase
{
    protected $userController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userController = new UserController();
    }

    /** @test */
    public function test_controller_can_be_instantiated()
    {
        // Verifica se o controller pode ser instanciado
        $this->assertInstanceOf(UserController::class, $this->userController);
    }

    /** @test */
    public function test_controller_has_index_method()
    {
        // Verifica se o método index existe
        $this->assertTrue(method_exists($this->userController, 'index'));
    }

    /** @test */
    public function test_controller_has_show_method()
    {
        // Verifica se o método show existe
        $this->assertTrue(method_exists($this->userController, 'show'));
    }

    /** @test */
    public function test_controller_has_store_method()
    {
        // Verifica se o método store existe
        $this->assertTrue(method_exists($this->userController, 'store'));
    }

    /** @test */
    public function test_controller_has_update_method()
    {
        // Verifica se o método update existe
        $this->assertTrue(method_exists($this->userController, 'update'));
    }

    /** @test */
    public function test_controller_has_destroy_method()
    {
        // Verifica se o método destroy existe
        $this->assertTrue(method_exists($this->userController, 'destroy'));
    }

    /** @test */
    public function test_controller_has_login_method()
    {
        // Verifica se o método login existe
        $this->assertTrue(method_exists($this->userController, 'login'));
    }

    /** @test */
    public function test_controller_has_logout_method()
    {
        // Verifica se o método logout existe
        $this->assertTrue(method_exists($this->userController, 'logout'));
    }

    /** @test */
    public function test_controller_has_logout_all_method()
    {
        // Verifica se o método logoutAll existe
        $this->assertTrue(method_exists($this->userController, 'logoutAll'));
    }

    /** @test */
    public function test_index_method_signature()
    {
        // Verifica a assinatura do método index
        $reflection = new \ReflectionMethod($this->userController, 'index');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_show_method_signature()
    {
        // Verifica a assinatura do método show
        $reflection = new \ReflectionMethod($this->userController, 'show');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_store_method_signature()
    {
        // Verifica a assinatura do método store
        $reflection = new \ReflectionMethod($this->userController, 'store');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_update_method_signature()
    {
        // Verifica a assinatura do método update
        $reflection = new \ReflectionMethod($this->userController, 'update');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(2, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_destroy_method_signature()
    {
        // Verifica a assinatura do método destroy
        $reflection = new \ReflectionMethod($this->userController, 'destroy');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_login_method_signature()
    {
        // Verifica a assinatura do método login
        $reflection = new \ReflectionMethod($this->userController, 'login');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_logout_method_signature()
    {
        // Verifica a assinatura do método logout
        $reflection = new \ReflectionMethod($this->userController, 'logout');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    /** @test */
    public function test_logout_all_method_signature()
    {
        // Verifica a assinatura do método logoutAll
        $reflection = new \ReflectionMethod($this->userController, 'logoutAll');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }
}