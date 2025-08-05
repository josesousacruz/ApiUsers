

# API de Usuários - Laravel

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Uma API RESTful completa para gerenciamento de usuários desenvolvida com Laravel 12, incluindo autenticação via Laravel Sanctum e documentação interativa com Swagger.

## 🚀 Características

- **CRUD Completo de Usuários**: Criar, listar, visualizar, atualizar e excluir usuários
- **Autenticação Segura**: Sistema de autenticação baseado em tokens usando Laravel Sanctum
- **Documentação Interativa**: Interface Swagger UI para testar e explorar a API
- **Validação de Dados**: Validação robusta de entrada com mensagens de erro detalhadas
- **Logs de Segurança**: Sistema de logging para auditoria e debugging
- **Estrutura RESTful**: Endpoints seguindo as melhores práticas REST

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- MySQL

## 🛠️ Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/josesousacruz/ApiUsers.git
   cd apiUsers
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Configure o ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure o banco de dados**
   Edite o arquivo `.env` com suas credenciais de banco de dados:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=apiusers
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

5. **Execute as migrações**
   ```bash
   php artisan migrate
   ```

6. **Inicie o servidor**
   ```bash
   php artisan serve
   ```

## 📚 Documentação da API

A documentação interativa da API está disponível através do Swagger UI:

- **Interface Swagger**: `http://localhost:8000/api/documentation`
- **JSON da API**: `http://localhost:8000/api/docs.json`

## 🔗 Endpoints Principais

### Autenticação
- `POST /api/user/register` - Registrar novo usuário
- `POST /api/user/login` - Fazer login
- `POST /api/user/logout` - Logout (requer autenticação)
- `POST /api/user/logout-all` - Logout de todos os dispositivos (requer autenticação)

### Usuários (Requer Autenticação)
- `GET /api/users` - Listar todos os usuários
- `GET /api/users/{id}` - Obter usuário específico
- `PUT /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Excluir usuário
- `GET /api/user` - Obter usuário autenticado

## 🔐 Autenticação

A API utiliza Laravel Sanctum para autenticação baseada em tokens. Para acessar endpoints protegidos:

1. Faça login através do endpoint `/api/user/login`
2. Use o token retornado no header `Authorization: Bearer {token}`

### Exemplo de Uso

```bash
# Login
curl -X POST http://localhost:8000/api/user/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@email.com","password":"senha123"}'

# Usar token para acessar endpoint protegido
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer {seu-token-aqui}"
```

## 📊 Estrutura de Resposta

Todas as respostas da API seguem um padrão consistente:

```json
{
  "success": true,
  "data": {},
  "message": "Operação realizada com sucesso"
}
```

### Códigos de Status HTTP
- `200` - Sucesso
- `201` - Criado com sucesso
- `401` - Não autorizado
- `404` - Não encontrado
- `422` - Erro de validação
- `500` - Erro interno do servidor

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter UserTest
```

## 📦 Dependências Principais

- **Laravel Framework** (^12.0) - Framework PHP
- **Laravel Sanctum** (^4.0) - Autenticação de API
- **L5-Swagger** (^9.0) - Documentação Swagger/OpenAPI
- **Laravel Tinker** (^2.10) - REPL para Laravel


