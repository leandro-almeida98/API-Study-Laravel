# 🚀 Task Manager API

RESTful API para gerenciamento de projetos e tarefas com Laravel 11.

## 🎯 Sobre o Projeto

Sistema completo de gerenciamento de tarefas com:

- ✅ Autenticação JWT com Laravel Sanctum
- 🔄 CRUD de Projetos e Tarefas
- 👥 Sistema de Equipes
- 💬 Comentários
- 📊 Dashboard com estatísticas

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
git clone https://github.com/seu-usuario/task-manager-api.git
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

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter Auth
```

## 📝 Licença

MIT

## 👤 Autor

Leandro Sacramento de Almeida - [GitHub](https://github.com/leandro-almeida98)

````

---

## ✅ Commit

```bash
git add .
git commit -m "feat: implementa sistema de autenticação com Sanctum

- Adiciona endpoints de registro, login e logout
- Cria validações customizadas para autenticação
- Implementa UserResource para padronizar respostas
- Adiciona testes automatizados para autenticação
- Atualiza documentação com exemplos de uso da API"

git push origin main
````
