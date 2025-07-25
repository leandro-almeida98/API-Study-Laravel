# 🚀 Task Manager API

RESTful API para gerenciamento de projetos e tarefas com Laravel 11.

## 🎯 Sobre o Projeto

Sistema completo de gerenciamento de tarefas com:

- ✅ Autenticação JWT com Laravel Sanctum
- ✅ CRUD completo de Projetos
- ✅ CRUD completo de Tarefas
- 🔄 Sistema de Equipes (em desenvolvimento)
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

# Filtros:
GET /api/v1/projects?status=active
GET /api/v1/projects?search=Laravel
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

### Tarefas

#### Listar Tarefas

```http
GET /api/v1/tasks
Authorization: Bearer {token}

# Filtros disponíveis:
GET /api/v1/tasks?status=todo
GET /api/v1/tasks?priority=high
GET /api/v1/tasks?project_id=1
GET /api/v1/tasks?assigned_to_me=1
GET /api/v1/tasks?overdue=1
```

**Resposta:**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Implementar autenticação",
      "description": "Criar sistema de login com JWT",
      "status": "in_progress",
      "priority": "high",
      "due_date": "2025-08-15",
      "estimated_hours": 8,
      "is_overdue": false,
      "days_until_due": 25,
      "project": {
        "id": 1,
        "name": "API Laravel"
      },
      "assigned_to": {
        "id": 2,
        "name": "Maria Santos",
        "email": "maria@example.com"
      },
      "created_by": {
        "id": 1,
        "name": "João Silva"
      },
      "created_at": "2024-01-19T20:00:00.000000Z",
      "updated_at": "2024-01-19T20:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Criar Tarefa

```http
POST /api/v1/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
  "project_id": 1,
  "title": "Implementar autenticação",
  "description": "Criar sistema de login com JWT",
  "status": "todo",
  "priority": "high",
  "assigned_to": 2,
  "due_date": "2025-08-15",
  "estimated_hours": 8
}
```

#### Ver Tarefa

```http
GET /api/v1/tasks/{id}
Authorization: Bearer {token}
```

#### Atualizar Tarefa

```http
PUT /api/v1/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Tarefa Atualizada",
  "status": "done",
  "priority": "medium"
}
```

#### Deletar Tarefa

```http
DELETE /api/v1/tasks/{id}
Authorization: Bearer {token}
```

---

## 🔐 Permissões

### Projetos

- **Criar:** Qualquer usuário autenticado
- **Visualizar:** Apenas o dono
- **Atualizar:** Apenas o dono
- **Deletar:** Apenas o dono

### Tarefas

- **Criar:** Qualquer usuário autenticado (em projetos próprios)
- **Visualizar:** Criador, usuário atribuído ou dono do projeto
- **Atualizar:** Criador, usuário atribuído ou dono do projeto
- **Deletar:** Criador ou dono do projeto

---

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter Auth
php artisan test --filter Project
php artisan test --filter Task

# Com detalhes
php artisan test --testdox

# Com coverage
php artisan test --coverage
```

## 📝 Licença

MIT

## 👤 Autor

Leandro Sacramento de Almeida - [GitHub](https://github.com/leandro-almeida98)

````

---

## ✅ Verificar Rotas

```bash
php artisan route:list --path=api/v1/tasks
````

Deve mostrar:

```
GET|HEAD   api/v1/tasks ............. tasks.index
POST       api/v1/tasks ............. tasks.store
GET|HEAD   api/v1/tasks/{task} ...... tasks.show
PUT|PATCH  api/v1/tasks/{task} ...... tasks.update
DELETE     api/v1/tasks/{task} ...... tasks.destroy
```

---

## 🧪 Rodar Testes

```bash
php artisan test --filter Task
```
