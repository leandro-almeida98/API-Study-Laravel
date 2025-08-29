# 🚀 Task Manager API

RESTful API completa para gerenciamento de projetos e tarefas com sistema de equipes e permissões hierárquicas.

## 🎯 Sobre o Projeto

Sistema profissional de gerenciamento de tarefas com:

- ✅ Autenticação JWT com Laravel Sanctum
- ✅ CRUD completo de Projetos
- ✅ CRUD completo de Tarefas
- ✅ Sistema de Comentários em Tarefas
- ✅ Sistema de Equipes com Membros
- ✅ Sistema de Permissões Hierárquico (Owner, Admin, Member, Viewer)
- ✅ Soft Deletes em todas as entidades
- ✅ 78 testes automatizados com Pest PHP

## 🛠️ Tecnologias

- **Backend:** PHP 8.3, Laravel 11
- **Banco de Dados:** MySQL 8.0
- **Autenticação:** Laravel Sanctum
- **Testes:** Pest PHP
- **Padrões:** RESTful API, Repository Pattern, Service Layer

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
DB_DATABASE=task_manager_api
DB_USERNAME=root
DB_PASSWORD=

# Rodar migrations
php artisan migrate

# (Opcional) Popular banco com dados de teste
php artisan db:seed

# Iniciar servidor
php artisan serve
```

## 📚 Documentação da API

### 🔐 Autenticação

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

#### Logout

```http
POST /api/v1/logout
Authorization: Bearer {token}
```

---

### 📁 Projetos

#### Listar Projetos

```http
GET /api/v1/projects
Authorization: Bearer {token}

# Filtros:
?status=active
?search=Laravel
```

#### Criar Projeto

```http
POST /api/v1/projects
Authorization: Bearer {token}

{
  "name": "API Laravel",
  "description": "Projeto de API REST",
  "team_id": 1,
  "status": "active",
  "deadline": "2025-12-31"
}
```

---

### ✅ Tarefas

#### Listar Tarefas

```http
GET /api/v1/tasks
Authorization: Bearer {token}

# Filtros:
?status=todo
?priority=high
?project_id=1
?assigned_to_me=1
?overdue=1
```

#### Criar Tarefa

```http
POST /api/v1/tasks
Authorization: Bearer {token}

{
  "project_id": 1,
  "title": "Implementar autenticação",
  "description": "Criar sistema de login",
  "status": "todo",
  "priority": "high",
  "assigned_to": 2,
  "due_date": "2025-08-15",
  "estimated_hours": 8
}
```

---

### 💬 Comentários

#### Listar Comentários de uma Tarefa

```http
GET /api/v1/tasks/{task_id}/comments
Authorization: Bearer {token}
```

#### Adicionar Comentário

```http
POST /api/v1/tasks/{task_id}/comments
Authorization: Bearer {token}

{
  "content": "Ótimo trabalho! Aprovado ✅"
}
```

---

### 👥 Equipes

#### Listar Equipes

```http
GET /api/v1/teams
Authorization: Bearer {token}
```

#### Criar Equipe

```http
POST /api/v1/teams
Authorization: Bearer {token}

{
  "name": "Equipe de Desenvolvimento",
  "description": "Time de backend"
}
```

#### Adicionar Membro

```http
POST /api/v1/teams/{team_id}/members
Authorization: Bearer {token}

{
  "user_id": 5,
  "role": "member"
}
```

**Roles disponíveis:**

- `owner` - Controle total
- `admin` - Gerenciar membros e projetos
- `member` - Gerenciar tarefas
- `viewer` - Apenas visualizar

---

## 🔐 Sistema de Permissões

### Owner (Proprietário)

- ✅ Gerenciar equipe (editar, deletar)
- ✅ Gerenciar membros (adicionar, remover, alterar roles)
- ✅ Gerenciar projetos
- ✅ Gerenciar tarefas
- ✅ Visualizar tudo

### Admin (Administrador)

- ✅ Gerenciar membros
- ✅ Gerenciar projetos
- ✅ Gerenciar tarefas
- ✅ Visualizar tudo

### Member (Membro)

- ✅ Gerenciar tarefas
- ✅ Visualizar tudo

### Viewer (Visualizador)

- ✅ Apenas visualizar

---

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter Auth
php artisan test --filter Project
php artisan test --filter Task
php artisan test --filter Comment
php artisan test --filter Team

# Com detalhes
php artisan test --testdox

# Com coverage
php artisan test --coverage
```

**Cobertura de Testes:**

- ✅ 78 testes automatizados
- ✅ Autenticação (12 testes)
- ✅ Projetos (12 testes)
- ✅ Tarefas (18 testes)
- ✅ Comentários (14 testes)
- ✅ Equipes (10 testes)
- ✅ Membros de Equipe (12 testes)

---

## 📊 Estrutura do Banco de Dados

```
users
├── id
├── name
├── email
├── password
└── timestamps

teams
├── id
├── name
├── description
├── owner_id → users
└── timestamps

team_members
├── id
├── team_id → teams
├── user_id → users
├── role (owner, admin, member, viewer)
└── joined_at

projects
├── id
├── user_id → users
├── team_id → teams
├── name
├── description
├── status
├── deadline
└── timestamps

tasks
├── id
├── project_id → projects
├── assigned_to → users
├── created_by → users
├── title
├── description
├── status
├── priority
├── due_date
├── estimated_hours
└── timestamps

comments
├── id
├── task_id → tasks
├── user_id → users
├── content
└── timestamps
```

---

## 🎯 Funcionalidades Principais

### ✅ Gerenciamento de Projetos

- Criar, editar, visualizar e deletar projetos
- Vincular projetos a equipes
- Filtrar por status e buscar por nome
- Soft delete

### ✅ Gerenciamento de Tarefas

- CRUD completo de tarefas
- Atribuir tarefas a usuários
- Definir prioridades e status
- Filtros avançados (status, prioridade, projeto, atrasadas)
- Estimativa de horas

### ✅ Sistema de Comentários

- Comentar em tarefas
- Editar e deletar próprios comentários
- Histórico completo de discussões

### ✅ Sistema de Equipes

- Criar e gerenciar equipes
- Adicionar e remover membros
- Sistema de permissões hierárquico
- Vincular projetos a equipes

---

## 📝 Licença

MIT

## 👤 Autor

**Leandro Sacramento de Almeida**

- GitHub: [@leandro-almeida98](https://github.com/leandro-almeida98)
- LinkedIn: https://www.linkedin.com/leandro-almeida2698/
- Email: leandro.sacramento98@gmail.com

---

## 🙏 Agradecimentos

Projeto desenvolvido como portfólio profissional demonstrando conhecimentos em:

- Laravel 11
- RESTful APIs
- Autenticação JWT
- Testes Automatizados
- Arquitetura de Software
- Banco de Dados Relacional
- Git & GitHub

---

⭐ Se este projeto foi útil, considere dar uma estrela!
