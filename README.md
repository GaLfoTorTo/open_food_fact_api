# OPEN FOOD FACT API

API RESTFUL para gerenciamento de produtos alimentícios, desenvolvida como solução para o desafio técnico da Coodesh. A aplicação integra com dados do Open Food Facts. Importação automatica, Listagem, Visualização, Atualização e Deleção de Produtos em base de dados interna.

## Tecnologias

- **PHP 8.2+** - Linguagem de programação
- **Laravel 10+** - Framework PHP
- **MySQL 8.0** - Banco de dados relacional
- **Docker & Docker Compose** - Containerização
- **Laravel Sanctum** - Autenticação via API Key
- **Guzzle HTTP** - Cliente para requisições HTTP
- **PHPUnit** - Testes unitários

## Requisitos

- PHP 8.2+
- Composer
- MY SQL
- Docker e Docker Compose instalados
- Git para clonar o repositório
- 2GB de RAM livre para containers
- Portas 8000 e 3306 disponíveis

## Instalação

1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/open_food_fact_api.git
```

2. Instale dependências
```bash
composer install
```

3. Atualização de .env
```bash
cp .env.example .env
```

4. Gere chave da aplicação
```bash
php artisan key:generate
```

5. Execute migrações
```bash
php artisan migrate --seed
```

6. Inicie o servidor
```bash
php artisan serve
```

7. Iniciar primeira importação (opcional)
```bash
php artisan products:import
```