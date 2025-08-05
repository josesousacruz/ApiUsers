<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="API de Usuários",
 *     version="1.0.0",
 *     description="API para gerenciamento de usuários com autenticação"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Servidor de desenvolvimento"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Modelo de usuário",
 *     @OA\Property(property="id", type="integer", example=1, description="ID único do usuário"),
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@email.com", description="Email do usuário"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Data de verificação do email"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Data de criação"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Data de atualização"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, description="Data de exclusão (soft delete)")
 * )
 * 
 * @OA\Schema(
 *     schema="UserCreate",
 *     type="object",
 *     title="UserCreate",
 *     description="Dados para criação de usuário",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@email.com", description="Email do usuário"),
 *     @OA\Property(property="password", type="string", format="password", example="senha123", description="Senha do usuário (mínimo 8 caracteres)")
 * )
 * 
 * @OA\Schema(
 *     schema="UserUpdate",
 *     type="object",
 *     title="UserUpdate",
 *     description="Dados para atualização de usuário",
 *     @OA\Property(property="name", type="string", example="João Silva", description="Nome completo do usuário"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@email.com", description="Email do usuário"),
 *     @OA\Property(property="password", type="string", format="password", example="novaSenha123", description="Nova senha do usuário (mínimo 8 caracteres)")
 * )
 * 
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="LoginRequest",
 *     description="Dados para login",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="joao@email.com", description="Email do usuário"),
 *     @OA\Property(property="password", type="string", format="password", example="senha123", description="Senha do usuário")
 * )
 * 
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     title="LoginResponse",
 *     description="Resposta do login",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Login realizado com sucesso"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/User"),
 *         @OA\Property(property="token", type="string", example="1|abc123def456...", description="Token de acesso")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="SuccessResponse",
 *     description="Resposta de sucesso padrão",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso")
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="ErrorResponse",
 *     description="Resposta de erro padrão",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Erro ao processar solicitação"),
 *     @OA\Property(property="error", type="string", example="Detalhes do erro")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="ValidationErrorResponse",
 *     description="Resposta de erro de validação",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Dados de entrada inválidos"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         example={
 *             "email": {"O campo email é obrigatório."},
 *             "password": {"O campo password deve ter pelo menos 8 caracteres."}
 *         },
 *         description="Detalhes dos erros de validação"
 *     )
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Listar todos os usuários",
     *     description="Retorna a lista de todos os usuários cadastrados",
     *     tags={"Usuários"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de usuarios encontrados com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Lista de usuarios encontrados com sucesso'
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao buscar usuarios: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar usuarios',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Buscar usuário por ID",
     *     description="Retorna um usuário específico pelo seu ID",
     *     tags={"Usuários"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario 1 encontrado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario ' . $id . ' encontrado com sucesso'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        } catch (Exception $e) {
            Log::error('Erro ao buscar usuário ID ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar usuário',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Criar novo usuário",
     *     description="Cria um novo usuário no sistema",
     *     tags={"Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserCreate")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario criado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Usuario criado com sucesso'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao criar usuário',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Atualizar usuário",
     *     description="Atualiza os dados de um usuário específico",
     *     tags={"Usuários"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/UserUpdate")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario 1 atualizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8',
            ]);

            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
            ]);

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'Usuario atualizado com sucesso'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar usuário ID ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao atualizar usuário',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Deletar usuário (soft delete)",
     *     description="Realiza soft delete de um usuário específico",
     *     tags={"Usuários"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário deletado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario ' . $id . ' deletado com sucesso'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        } catch (Exception $e) {
            Log::error('Erro ao deletar usuário ID ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao deletar usuário',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     summary="Login do usuário",
     *     description="Realiza login do usuário e retorna token de autenticação",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            // Revoga todos os tokens existentes do usuário
            $user->tokens()->delete();

            // Cria novo token
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ],
                'message' => 'Login realizado com sucesso'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erro ao fazer login: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao fazer login',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/logout",
     *     summary="Logout do usuário",
     *     description="Realiza logout do usuário revogando o token atual",
     *     tags={"Autenticação"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoga o token atual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao fazer logout: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao fazer logout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/logout-all",
     *     summary="Logout de todos os dispositivos",
     *     description="Revoga todos os tokens do usuário, fazendo logout de todos os dispositivos",
     *     tags={"Autenticação"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout de todos os dispositivos realizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            // Revoga todos os tokens do usuário
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout de todos os dispositivos realizado com sucesso'
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao fazer logout de todos os dispositivos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao fazer logout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
