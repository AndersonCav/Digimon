# Digimon Search

Aplicação web em PHP para pesquisar Digimons via API externa, com autenticação de usuários e favoritos persistidos em MySQL.

## Stack

- PHP (sem framework)
- MySQL
- Bootstrap 5

## O que o projeto entrega hoje

- Busca de Digimons por nome, nível e tipo (combinando filtros).
- Paginação dos resultados retornados pela API.
- Login e cadastro de usuário com hash de senha.
- Sessão com regeneração de ID após login.
- Rotas restritas para área autenticada.
- Favoritar e remover favoritos de forma completa.
- Exibição somente dos favoritos do usuário autenticado.
- Cache local em arquivo para respostas da API com TTL configurável.
- Configuração por ambiente via arquivo .env.

## O que não está neste projeto

- Testes automatizados.
- Painel administrativo.
- Framework full-stack (Laravel/Symfony).

## Estrutura principal

```text
Digimon/
├── config/
│   ├── bootstrap.php
│   ├── config.php
│   └── env.php
├── database/
│   ├── schema.sql
│   └── seed_example.sql
├── public/
│   ├── dashboard.php
│   ├── favorite_action.php
│   ├── favoritos.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   └── style.css
├── src/
│   ├── Auth.php
│   ├── DigimonApi.php
│   ├── FavoriteService.php
│   ├── helpers.php
│   └── search.php
├── storage/
│   └── cache/
├── templates/
│   ├── footer.php
│   ├── header.php
│   └── search_results.php
├── .env.example
└── .gitignore
```

## Como rodar localmente

1. Clone o repositório:

```bash
git clone <url-do-seu-repositorio>
cd Digimon
```

1. Crie seu arquivo de ambiente a partir do exemplo:

```bash
cp .env.example .env
```

No Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

1. Configure os valores do .env conforme seu ambiente local (host, porta, usuário e senha do MySQL).

1. Crie o banco e tabelas:

```bash
mysql -u root -p < database/schema.sql
```

1. (Opcional) Inserir dados de exemplo:

```bash
mysql -u root -p < database/seed_example.sql
```

1. Sirva o projeto no Apache/XAMPP e acesse:

```text
http://localhost/Digimon/public/index.php
```

## Configurações de ambiente

As configurações ficam no arquivo .env:

- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASSWORD
- DB_CHARSET
- DIGIMON_API_URL
- CACHE_ENABLED (1 ou 0)
- CACHE_TTL_SECONDS
- LOG_ENABLED (1 ou 0)
- SESSION_SECURE_COOKIE (1 em HTTPS, 0 em HTTP local)
- SESSION_SAMESITE (Lax, Strict ou None)

## Segurança aplicada

- Uso de prepared statements no MySQL.
- password_hash e password_verify para senha.
- Regeneração de sessão no login.
- Endurecimento de cookie de sessão (HttpOnly, SameSite, strict mode).
- Logout com limpeza completa de sessão/cookie.
- Escaping de saída HTML com helper dedicado.
- Token CSRF em formulários sensíveis (login, cadastro e favoritos).
- Logging interno de erros em storage/logs/app.log.

## Observações de portfólio

Este projeto foi organizado para demonstrar fundamentos sólidos de aplicação PHP tradicional: separação mínima de responsabilidades, fluxo de autenticação consistente, busca externa com tratamento de falhas e persistência de favoritos com escopo por usuário.
