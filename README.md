# 🚀 Task Manager API

RESTful API para gerenciamento de projetos e tarefas com Laravel 11.

## 🎯 Sobre o Projeto

Sistema completo de gerenciamento de tarefas com:

- ✅ Autenticação JWT com Laravel Sanctum
- ✅ CRUD completo de Projetos
- 🔄 CRUD de Tarefas (em desenvolvimento)
- 👥 Sistema de Equipes (em desenvolvimento)
- 💬 Comentários (em desenvolvimento)
- 📊 Dashboard com estatísticas (em desenvolvimento)

## 🛠️ Tecnologias

- PHP 8.3
- Laravel 11
- MySQL 8.0
- Laravel Sanctum
- Pest PHP

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- MySQL >= 8.0

## 🚀 Instalação

```bash
# Clonar repositório
git clone https://github.com/leandro-almeida98/task-manager-api.git
cd task-manager-api

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados no .env
# DB_DATABASE=task_manager_api
# DB_USERNAME=root
# DB_PASSWORD=

# Rodar migrations
php artisan migrate

# Iniciar servidor
php artisan serve
```

## 📚 Documentação da API

### Autenticação

#### Registrar Usuário

```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

**Resposta:**

```json
{
    "message": "Usuário registrado com sucesso!",
    "data": {
        "user": {
            "id": 1,
            "name": "João Silva",
            "email": "joao@example.com",
            "created_at": "2024-01-19T20:00:00.000000Z",
            "updated_at": "2024-01-19T20:00:00.000000Z"
        },
        "token": "1|abc123..."
    }
}
```

#### Login

```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "joao@example.com",
  "password": "senha123"
}
```

#### Ver Perfil

```http
GET /api/v1/me
Authorization: Bearer {token}
```

#### Logout

```http
POST /api/v1/logout
Authorization: Bearer {token}
```

---

### Projetos

#### Listar Projetos

```http
GET /api/v1/projects
Authorization: Bearer {token}

# Filtros opcionais:
GET /api/v1/projects?status=active
GET /api/v1/projects?search=Laravel
```

**Resposta:**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Projeto Laravel",
      "description": "Descrição do projeto",
      "status": "active",
      "deadline": "2025-12-31",
      "is_overdue": false,
      "days_until_deadline": 150,
      "owner": {
        "id": 1,
        "name": "João Silva",
        "email": "joao@example.com"
      },
      "created_at": "2024-01-19T20:00:00.000000Z",
      "updated_at": "2024-01-19T20:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Criar Projeto

```http
POST /api/v1/projects
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Novo Projeto",
  "description": "Descrição do projeto",
  "status": "active",
  "deadline": "2025-12-31"
}
```

#### Ver Projeto

```http
GET /api/v1/projects/{id}
Authorization: Bearer {token}
```

#### Atualizar Projeto

```http
PUT /api/v1/projects/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Projeto Atualizado",
  "status": "completed"
}
```

#### Deletar Projeto

```http
DELETE /api/v1/projects/{id}
Authorization: Bearer {token}
```

---

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter Auth
php artisan test --filter Project

# Com detalhes
php artisan test --testdox

# Com coverage
php artisan test --coverage
```

## 📝 Licença

MIT

## 👤 Autor

Leandro Sacramento de Almeida - [GitHub](https://github.com/leandro-almeida98)
