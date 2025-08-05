<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Rotas públicas (sem autenticação)
 * 
 * POST   /api/user/register - Registrar novo usuário
 * POST   /api/user/login    - Fazer login
 */
Route::post('/user/register', [UserController::class, 'store']);
Route::post('/user/login', [UserController::class, 'login']);

/**
 * Rotas protegidas por Sanctum (requerem autenticação)
 */
Route::middleware('auth:sanctum')->group(function () {
    /**
     *  Documentação das rotas:
     * GET    /api/user - Obter usuário autenticado
     */
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    /**
     * PROTEGIDAS (requerem token Sanctum):
     * 
     * GET    /api/users         - Listar todos os usuários (index)
     * POST   /api/users         - Criar novo usuário (store) - disponível também como rota pública
     * GET    /api/users/{id}    - Obter usuário específico (show)
     * PUT    /api/users/{id}    - Atualizar usuário (update)
     * DELETE /api/users/{id}    - Deletar usuário com soft delete (destroy)
     */
    Route::apiResource('users', UserController::class);

    /**
     * POST   /api/user/logout   - Fazer logout
     * POST   /api/user/logout-all - Fazer logout de todos os dispositivos
     */
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::post('/user/logout-all', [UserController::class, 'logoutAll']);
});

