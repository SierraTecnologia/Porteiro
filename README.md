# SierraTecnologia Porteiro

**SierraTecnologia Porteiro** - Biblioteca completa de autenticação, autorização e gerenciamento de usuários para aplicações Laravel.

[![Packagist](https://img.shields.io/packagist/v/sierratecnologia/porteiro.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/sierratecnologia/porteiro)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/sierratecnologia/porteiro.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/sierratecnologia/porteiro/)
[![CI](https://github.com/SierraTecnologia/Porteiro/actions/workflows/ci.yml/badge.svg)](https://github.com/SierraTecnologia/Porteiro/actions/workflows/ci.yml)
[![StyleCI](https://styleci.io/repos/60968880/shield)](https://styleci.io/repos/60968880)
[![License](https://img.shields.io/packagist/l/sierratecnologia/porteiro.svg?label=License&style=flat-square)](https://github.com/sierratecnologia/porteiro/blob/master/LICENSE)

---

## Sumário

- [Introdução](#introdução)
- [Instalação](#instalação)
- [Arquitetura e Estrutura Interna](#arquitetura-e-estrutura-interna)
- [Principais Componentes e Responsabilidades](#principais-componentes-e-responsabilidades)
- [Uso Prático](#uso-prático)
- [Integração com o Ecossistema SierraTecnologia](#integração-com-o-ecossistema-sierratecnologia)
- [Extensão e Customização](#extensão-e-customização)
- [Exemplos Reais](#exemplos-reais)
- [Ferramentas de Qualidade](#ferramentas-de-qualidade)
- [Guia de Contribuição](#guia-de-contribuição)

---

## Introdução

### O que é a biblioteca Porteiro

O **Porteiro** é uma biblioteca Laravel desenvolvida pela SierraTecnologia que fornece uma solução completa e integrada para autenticação, autorização e gerenciamento de usuários. A biblioteca implementa um sistema robusto de controle de acesso baseado em papéis (RBAC - Role-Based Access Control), com suporte a múltiplos provedores de autenticação social, ativação de contas por e-mail e gerenciamento granular de permissões.

### Objetivo e filosofia do projeto

O Porteiro foi desenvolvido com os seguintes objetivos:

- **Padronização**: Fornecer uma solução consistente de autenticação para todos os projetos da SierraTecnologia
- **Reutilização**: Eliminar código duplicado entre projetos através de uma biblioteca centralizada
- **Flexibilidade**: Permitir customizações sem modificar o código-fonte da biblioteca
- **Segurança**: Implementar as melhores práticas de segurança em autenticação e autorização
- **Produtividade**: Acelerar o desenvolvimento de novas aplicações com funcionalidades prontas para uso

### Benefícios de uso em aplicações Laravel

- Sistema completo de autenticação (login, registro, reset de senha, ativação por e-mail)
- Autenticação social via OAuth (GitHub, Google, Facebook, Twitter, LinkedIn, WeChat)
- Sistema de papéis e permissões granular
- Middlewares prontos para diferentes níveis de acesso (Admin, User, Client, Master)
- API authentication via Laravel Passport
- Notificações por e-mail configuráveis
- Rastreamento de atividades de usuários
- Suporte a avatares via Gravatar
- Integração com Elasticsearch para busca de usuários

### Como se encaixa no ecossistema SierraTecnologia

O Porteiro é um componente fundamental do ecossistema SierraTecnologia, servindo como base de autenticação para diversos outros pacotes:

- **Pedreiro**: Fornece models base e funcionalidades compartilhadas
- **Muleta**: Utilitários e helpers comuns
- **Crypto**: Criptografia e segurança de dados

---

## Instalação

### Requisitos mínimos

- PHP >= 7.2 | 8.0+
- Laravel >= 7.x | 8.x | 9.x | 10.x
- Extensões PHP: curl, json, mbstring

### Instalação via Composer

```bash
composer require sierratecnologia/porteiro
```

### Publicação de assets e configurações

Publique os arquivos de configuração, views e migrations:

```bash
# Publicar configuração
php artisan vendor:publish --provider="Porteiro\PorteiroProvider" --tag=config

# Publicar views (opcional, para customização)
php artisan vendor:publish --provider="Porteiro\PorteiroProvider" --tag=views

# Publicar migrations
php artisan vendor:publish --provider="Porteiro\PorteiroProvider" --tag=migrations

# Executar migrations
php artisan migrate
```

### Registro de Service Providers

O Porteiro utiliza auto-discovery do Laravel, portanto o Service Provider e Facade são registrados automaticamente. Caso necessário, adicione manualmente:

```php
// config/app.php

'providers' => [
    // ...
    Porteiro\PorteiroProvider::class,
],

'aliases' => [
    // ...
    'Porteiro' => Porteiro\Facades\Porteiro::class,
],
```

---

## Arquitetura e Estrutura Interna

### Descrição dos diretórios principais

```
src/
├── Activation/         # Workflow de ativação de contas por e-mail
├── Auth/               # Interfaces e políticas de autenticação
├── Console/            # Comandos Artisan
├── Contracts/          # Contratos e interfaces
├── Exceptions/         # Classes de exceção customizadas
├── Facades/            # Laravel Facades
├── Http/
│   ├── Controllers/    # Controllers organizados por domínio
│   ├── Middleware/     # Middlewares de proteção de rotas
│   └── Policies/       # Políticas de autorização
├── Models/             # Models Eloquent
├── Notifications/      # Notificações por e-mail
├── Observers/          # Observers de models
├── Services/           # Camada de lógica de negócio
└── Traits/             # Traits reutilizáveis
```

### Padrões arquiteturais adotados

O Porteiro segue uma arquitetura em camadas (Layered Architecture) com os seguintes padrões:

**MVC + Service Layer**
```
Routes → Controllers → Services → Models → Database
```

**Principais padrões utilizados:**

- **Service Layer**: Lógica de negócio encapsulada em Services
- **Repository Pattern**: Abstração de acesso a dados (via Services)
- **Dependency Injection**: Injeção de dependências via construtor
- **Contract-Based Design**: Uso de interfaces para desacoplamento
- **Trait Composition**: Funcionalidades reutilizáveis via Traits

### Comunicação entre as camadas

1. **Controllers** recebem requisições HTTP e delegam para Services
2. **Services** contêm a lógica de negócio e manipulam Models
3. **Models** representam entidades e relacionamentos do banco
4. **Policies** definem regras de autorização
5. **Middlewares** protegem rotas baseado em papéis
6. **Notifications** enviam e-mails em resposta a eventos

### Convenções da SierraTecnologia

- Namespaces seguem padrão PSR-4
- Models estendem de `Pedreiro\Models\Base`
- Services são injetados via construtor
- Traits são usados para composição de funcionalidades
- Notificações são queueable por padrão

---

## Principais Componentes e Responsabilidades

### Models

#### User (`Porteiro\Models\User`)

Model principal de usuário com autenticação completa:

```php
use Porteiro\Models\User;

// Criar usuário
$user = User::create([
    'name' => 'João Silva',
    'email' => 'joao@exemplo.com',
    'password' => bcrypt('senha123'),
]);

// Verificar papel
if ($user->hasRole('admin')) {
    // Acesso administrativo
}

// Verificar permissão
if ($user->hasPermission('edit_users')) {
    // Pode editar usuários
}

// Atribuir papel
$user->setRole('editor');
```

**Traits utilizados:**
- `Authenticatable` - Autenticação Laravel
- `HasApiTokens` - Tokens de API (Passport)
- `PorteiroUser` - Papéis e permissões
- `UserSocialiteHelper` - Autenticação social
- `UserAvatarHelper` - Gerenciamento de avatar
- `UserActivityHelper` - Rastreamento de atividades

#### Role (`Porteiro\Models\Role`)

Representa papéis de usuário:

```php
use Porteiro\Models\Role;

// Criar papel
$role = Role::create([
    'name' => 'editor',
    'label' => 'Editor de Conteúdo',
]);

// Associar permissões
$role->permissions()->sync([1, 2, 3]);

// Buscar usuários com papel
$editors = $role->users;
```

#### Permission (`Porteiro\Models\Permission`)

Representa permissões granulares:

```php
use Porteiro\Models\Permission;

// Gerar permissões CRUD para uma tabela
Permission::generateFor('posts');
// Cria: browse_posts, read_posts, edit_posts, add_posts, delete_posts

// Remover permissões
Permission::removeFrom('posts');
```

### Services

#### PorteiroService

Serviço principal para resolução de models:

```php
use Porteiro\Facades\Porteiro;

// Obter classe do model (permite override na aplicação)
$userClass = Porteiro::modelClass('User');
// Retorna App\Models\User ou Porteiro\Models\User
```

#### UserService

Gerenciamento de usuários:

```php
use Porteiro\Services\UserService;

class MinhaController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        // Listar todos usuários
        $users = $this->userService->all();

        // Buscar por ID
        $user = $this->userService->find(1);

        // Buscar por papel
        $admins = $this->userService->findByRoleID(2);

        // Criar usuário
        $user = $this->userService->create([
            'name' => 'Novo Usuário',
            'email' => 'novo@exemplo.com',
            'password' => 'senha123',
        ]);
    }
}
```

#### RoleService

Gerenciamento de papéis:

```php
use Porteiro\Services\RoleService;

$roleService = app(RoleService::class);

// Criar papel com permissões
$role = $roleService->create([
    'name' => 'moderator',
    'label' => 'Moderador',
    'permissions' => [1, 2, 3], // IDs das permissões
]);

// Atualizar papel
$roleService->update($roleId, [
    'label' => 'Super Moderador',
    'permissions' => [1, 2, 3, 4, 5],
]);
```

#### ActivateService

Ativação de contas por e-mail:

```php
use Porteiro\Services\ActivateService;

$activateService = app(ActivateService::class);

// Enviar token de ativação
$activateService->sendActivationToken($user);

// Ativar usuário com token
$activateService->activateUser($token);
```

### Middlewares

O Porteiro fornece middlewares para diferentes níveis de acesso:

```php
// routes/web.php

// Rotas protegidas para usuários autenticados
Route::middleware(['porteiro.user'])->group(function () {
    Route::get('/perfil', [ProfileController::class, 'index']);
});

// Rotas administrativas
Route::middleware(['porteiro.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Rotas de painel
Route::middleware(['porteiro.painel'])->group(function () {
    Route::get('/painel', [PainelController::class, 'index']);
});

// Rotas de cliente
Route::middleware(['porteiro.client'])->group(function () {
    Route::get('/cliente', [ClientController::class, 'index']);
});

// Rotas master (superadmin)
Route::middleware(['porteiro.master'])->group(function () {
    Route::get('/master', [MasterController::class, 'index']);
});
```

### Traits

#### PorteiroUser

Adiciona funcionalidades de papéis e permissões ao model User:

```php
use Porteiro\Traits\PorteiroUser;

class User extends Authenticatable
{
    use PorteiroUser;

    // Agora tem acesso a:
    // $user->role() - papel padrão
    // $user->roles() - todos os papéis
    // $user->hasRole('admin')
    // $user->hasPermission('edit_posts')
    // $user->setRole('editor')
}
```

#### HasRoles

Para Services que gerenciam atribuição de papéis:

```php
use Porteiro\Traits\HasRoles;

class MeuService
{
    use HasRoles;

    public function promoverUsuario($userId)
    {
        $this->assignRole('admin', $userId);
    }

    public function rebaixarUsuario($userId)
    {
        $this->unassignRole('admin', $userId);
        $this->assignRole('user', $userId);
    }
}
```

#### UserSocialiteHelper

Busca usuários por provider OAuth:

```php
use Porteiro\Traits\UserSocialiteHelper;

// No model User
$user = User::getByGithubId($githubId);
$user = User::getByGoogleId($googleId);
$user = User::getByFacebookId($facebookId);
$user = User::getByTwitterId($twitterId);
$user = User::getByLinkedinId($linkedinId);
```

---

## Uso Prático

### Configuração básica

Após a instalação, configure o arquivo `config/porteiro.php`:

```php
return [
    // Classes de model customizadas
    'models' => [
        'user' => App\Models\User::class,
        'role' => Porteiro\Models\Role::class,
        'permission' => Porteiro\Models\Permission::class,
    ],

    // Configurações de ativação
    'activation' => [
        'required' => true,
        'limit_attempts' => 3,
    ],

    // Redirecionamentos
    'redirects' => [
        'login' => '/painel',
        'logout' => '/',
    ],
];
```

### Exemplo de implementação completa

#### 1. Criar Model User customizado

```php
// app/Models/User.php

namespace App\Models;

use Porteiro\Models\User as PorteiroUser;

class User extends PorteiroUser
{
    protected $fillable = [
        'name', 'email', 'password', 'cpf', 'telefone',
    ];

    // Relacionamentos customizados
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
```

#### 2. Configurar rotas protegidas

```php
// routes/web.php

use App\Http\Controllers\PainelController;
use App\Http\Controllers\AdminController;

// Rotas públicas
Route::get('/', [HomeController::class, 'index']);

// Rotas de autenticação (já incluídas pelo Porteiro)

// Painel do usuário
Route::middleware(['auth', 'porteiro.user'])->prefix('painel')->group(function () {
    Route::get('/', [PainelController::class, 'index'])->name('painel.index');
    Route::get('/perfil', [PainelController::class, 'perfil'])->name('painel.perfil');
    Route::put('/perfil', [PainelController::class, 'atualizarPerfil']);
});

// Área administrativa
Route::middleware(['auth', 'porteiro.admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::resource('usuarios', UserController::class);
    Route::resource('papeis', RoleController::class);
});
```

#### 3. Verificar permissões em Controllers

```php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Porteiro\Services\UserService;
use Porteiro\Services\RoleService;

class AdminController extends Controller
{
    protected $userService;
    protected $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function usuarios()
    {
        // Verificar permissão
        if (!auth()->user()->hasPermission('browse_users')) {
            abort(403, 'Sem permissão para visualizar usuários');
        }

        $usuarios = $this->userService->all();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function editarUsuario($id)
    {
        $usuario = $this->userService->find($id);

        // Usar Policy
        $this->authorize('edit', $usuario);

        $papeis = $this->roleService->all();
        return view('admin.usuarios.edit', compact('usuario', 'papeis'));
    }
}
```

#### 4. Verificar papéis em Views

```blade
{{-- resources/views/layouts/app.blade.php --}}

@auth
    <nav>
        <a href="{{ route('painel.index') }}">Painel</a>

        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.index') }}">Administração</a>
        @endif

        @if(auth()->user()->hasPermission('edit_posts'))
            <a href="{{ route('posts.create') }}">Novo Post</a>
        @endif
    </nav>
@endauth
```

### Boas práticas e recomendações

1. **Use Services para lógica de negócio**: Nunca coloque lógica complexa em Controllers
2. **Prefira Policies para autorização**: Use `$this->authorize()` em vez de verificações manuais
3. **Configure permissões granulares**: Crie permissões específicas em vez de apenas `admin/user`
4. **Use Traits para composição**: Adicione funcionalidades via Traits em vez de herança
5. **Customize via configuração**: Use `config/porteiro.php` em vez de modificar o código-fonte

---

## Integração com o Ecossistema SierraTecnologia

### Dependências do ecossistema

O Porteiro integra-se com outros pacotes SierraTecnologia:

```php
// composer.json
"require": {
    "sierratecnologia/crypto": "^0.4.0",   // Criptografia
    "sierratecnologia/muleta": "^0.4.0",   // Utilitários
    "sierratecnologia/pedreiro": "^0.4.0"  // Models base
}
```

### Padrões de versionamento

A SierraTecnologia segue Semantic Versioning (SemVer):

- **MAJOR**: Mudanças incompatíveis com versões anteriores
- **MINOR**: Novas funcionalidades compatíveis
- **PATCH**: Correções de bugs compatíveis

### Padrões de CI/CD

Todos os pacotes SierraTecnologia utilizam:

- **GitHub Actions**: Testes automatizados, PHPStan, PHPCS
- **Scrutinizer**: Análise de qualidade de código
- **StyleCI**: Formatação automática de código
- **Packagist**: Distribuição via Composer

### Uso padronizado entre equipes

Para garantir consistência entre projetos:

1. **Mesmo versionamento**: Use a mesma versão do Porteiro em todos os projetos
2. **Configuração centralizada**: Mantenha configurações padrão em templates
3. **Customizações via config**: Evite modificar código-fonte da biblioteca
4. **Testes de integração**: Teste a integração com outros pacotes Sierra

---

## Extensão e Customização

### Sobrescrever Models

Crie seus próprios models e configure no `config/porteiro.php`:

```php
// app/Models/User.php
namespace App\Models;

use Porteiro\Models\User as BaseUser;

class User extends BaseUser
{
    // Adicione campos
    protected $fillable = [
        'name', 'email', 'password', 'empresa_id', 'departamento',
    ];

    // Adicione relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Sobrescreva métodos
    public function hasRole($name)
    {
        // Lógica customizada
        if ($this->is_superadmin) {
            return true;
        }
        return parent::hasRole($name);
    }
}
```

```php
// config/porteiro.php
return [
    'models' => [
        'user' => App\Models\User::class,
    ],
];
```

### Criar novos middlewares

```php
// app/Http/Middleware/VerificarAssinatura.php

namespace App\Http\Middleware;

use Closure;

class VerificarAssinatura
{
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->hasActiveSubscription()) {
            return redirect()->route('assinatura.expirada');
        }

        return $next($request);
    }
}
```

### Estender Services

```php
// app/Services/UserService.php

namespace App\Services;

use Porteiro\Services\UserService as BaseUserService;

class UserService extends BaseUserService
{
    public function createWithEmpresa(array $data, $empresaId)
    {
        $data['empresa_id'] = $empresaId;
        $user = $this->create($data);

        // Lógica adicional
        $this->notificarNovoUsuario($user);

        return $user;
    }

    protected function notificarNovoUsuario($user)
    {
        // Enviar notificação
    }
}
```

### Adicionar novos provedores OAuth

```php
// app/Traits/CustomSocialiteHelper.php

namespace App\Traits;

use Porteiro\Traits\UserSocialiteHelper;

trait CustomSocialiteHelper
{
    use UserSocialiteHelper;

    public static function getBySlackId($id)
    {
        return static::where('slack_id', $id)->first();
    }

    public static function getByDiscordId($id)
    {
        return static::where('discord_id', $id)->first();
    }
}
```

---

## Exemplos Reais

### Caso 1: Sistema de gestão empresarial

**Cenário**: Sistema com múltiplos níveis de acesso (admin, gerente, funcionário)

```php
// Configuração de papéis
$admin = Role::create(['name' => 'admin', 'label' => 'Administrador']);
$gerente = Role::create(['name' => 'gerente', 'label' => 'Gerente']);
$funcionario = Role::create(['name' => 'funcionario', 'label' => 'Funcionário']);

// Permissões
Permission::generateFor('funcionarios');
Permission::generateFor('relatorios');
Permission::generateFor('financeiro');

// Atribuir permissões
$admin->permissions()->sync(Permission::all()->pluck('id'));
$gerente->permissions()->sync([
    Permission::where('name', 'browse_funcionarios')->first()->id,
    Permission::where('name', 'browse_relatorios')->first()->id,
]);

// Uso em controller
public function relatorioFinanceiro()
{
    if (!auth()->user()->hasPermission('browse_financeiro')) {
        abort(403);
    }

    return view('relatorios.financeiro');
}
```

### Caso 2: Plataforma multi-tenant

**Cenário**: Cada empresa tem seus próprios usuários e administradores

```php
// Model User customizado
class User extends PorteiroUser
{
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function hasRole($name)
    {
        // Verificar papel dentro da empresa
        return $this->roles()
            ->where('empresa_id', $this->empresa_id)
            ->where('name', $name)
            ->exists();
    }
}

// Middleware de tenant
class EmpresaMiddleware
{
    public function handle($request, Closure $next)
    {
        $empresaId = auth()->user()->empresa_id;
        app()->instance('empresa_id', $empresaId);

        return $next($request);
    }
}
```

### Caso 3: API com autenticação OAuth

**Cenário**: API REST com autenticação via Laravel Passport

```php
// routes/api.php
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });

    Route::middleware('can:browse_posts')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
    });
});

// Controller
class PostController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return Post::all();
        }

        return Post::where('user_id', $user->id)->get();
    }
}
```

### Benefícios observados

| Aspecto | Antes | Depois |
|---------|-------|--------|
| Código duplicado | 500+ linhas por projeto | 0 linhas (biblioteca) |
| Tempo de setup | 2-3 dias | 30 minutos |
| Bugs de autenticação | Frequentes | Raros |
| Manutenção | Individual por projeto | Centralizada |
| Consistência | Variável | Padronizada |

---

## Ferramentas de Qualidade

### Executar localmente

```bash
# Instalar dependências
composer install

# Executar testes
./vendor/bin/phpunit

# Executar com cobertura
./vendor/bin/phpunit --coverage-html coverage

# Análise estática (PHPStan)
./vendor/bin/phpstan analyse src/ --level=5

# Verificar padrão de código (PHPCS)
./vendor/bin/phpcs --standard=PSR12 src/

# Corrigir automaticamente (PHP-CS-Fixer)
./vendor/bin/php-cs-fixer fix --allow-risky=yes

# Análise de tipos (Psalm)
./vendor/bin/psalm
```

### GitHub Actions

O projeto utiliza CI/CD automatizado com os seguintes jobs:

- **Tests**: PHPUnit em PHP 8.2/8.3 com Laravel 10/11
- **PHPStan**: Análise estática nível 5
- **PHPCS**: Verificação PSR-12
- **Psalm**: Análise de tipos

### Configurações de qualidade

**PHPStan** (`phpstan.neon.dist`):
```neon
parameters:
    level: 5
    paths:
        - src
```

**PHPCS** (`phpcs.xml`):
```xml
<ruleset name="SierraTecnologia">
    <rule ref="PSR12"/>
    <file>src</file>
</ruleset>
```

**PHPUnit** (`phpunit.xml`):
```xml
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Porteiro Test Suite">
            <directory>tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

---

## Guia de Contribuição

### Como contribuir

1. **Fork** o repositório
2. **Clone** seu fork: `git clone https://github.com/seu-usuario/porteiro.git`
3. **Crie uma branch**: `git checkout -b feature/nova-funcionalidade`
4. **Faça suas alterações** seguindo os padrões do projeto
5. **Execute os testes**: `./vendor/bin/phpunit`
6. **Commit**: `git commit -m "feat: adiciona nova funcionalidade"`
7. **Push**: `git push origin feature/nova-funcionalidade`
8. **Abra um Pull Request**

### Padrões de commit

Seguimos o padrão [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação de código
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Tarefas de manutenção

### Branch naming

- `feature/descricao-da-feature` - Novas funcionalidades
- `fix/descricao-do-bug` - Correções
- `docs/descricao` - Documentação
- `refactor/descricao` - Refatorações

### Executar testes localmente

```bash
# Clonar repositório
git clone https://github.com/SierraTecnologia/Porteiro.git
cd Porteiro

# Instalar dependências
composer install

# Executar todos os testes
./vendor/bin/phpunit

# Executar teste específico
./vendor/bin/phpunit --filter NomeDoTeste

# Verificar qualidade
./vendor/bin/phpstan analyse src/
./vendor/bin/phpcs --standard=PSR12 src/
```

### Issues e Pull Requests

- **Issues**: Use para reportar bugs ou sugerir funcionalidades
- **Pull Requests**: Devem incluir testes e documentação
- **Code Review**: Todos os PRs passam por revisão

### Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

### Contato

- **E-mail**: help@sierratecnologia.com.br
- **Slack**: [SierraTecnologia Slack](https://bit.ly/sierratecnologia-slack)
- **Twitter**: [@sierratecnologia](https://twitter.com/sierratecnologia)

---

## Changelog

Consulte o [CHANGELOG.md](CHANGELOG.md) para histórico completo de versões.

---

## Support

Os seguintes canais de suporte estão disponíveis:

- [Chat no Slack](https://bit.ly/sierratecnologia-slack)
- [Ajuda por E-mail](mailto:help@sierratecnologia.com.br)
- [Siga no Twitter](https://twitter.com/sierratecnologia)

---

## Security Vulnerabilities

Se você descobrir uma vulnerabilidade de segurança, envie um e-mail para [help@sierratecnologia.com.br](mailto:help@sierratecnologia.com.br). Todas as vulnerabilidades serão tratadas prontamente.

---

## About SierraTecnologia

SierraTecnologia is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Rio de Janeiro, Brazil since June 2008. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That's how we do our share of advancing humanity.

---

## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2008-2024 SierraTecnologia, Some rights reserved.
