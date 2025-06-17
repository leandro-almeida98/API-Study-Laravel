# 🚀 Task Manager API

RESTful API para gerenciamento de projetos e tarefas com Laravel 11.

## 🎯 Sobre o Projeto

Sistema completo de gerenciamento de tarefas com:

- Autenticação JWT
- CRUD de Projetos e Tarefas
- Sistema de Equipes
- Comentários
- Dashboard com estatísticas

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

## 📚 Documentação

Em desenvolvimento...

## 🧪 Testes

```bash
php artisan test
```

## 📝 Licença

MIT

## 👤 Autor

Seu Nome - [GitHub](https://github.com/seu-usuario)

````

---

## ✅ Commit 1

```bash
git add .
git commit -m "feat: initial project setup and database configuration

- Configure Laravel Sanctum for API authentication
- Setup database connection
- Configure CORS
- Update User model with HasApiTokens
- Add comprehensive README.md"

git push origin main
````
