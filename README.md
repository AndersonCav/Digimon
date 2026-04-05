# Digimon Search

Aplicação web em PHP para pesquisar Digimons, consultar detalhes via API externa e salvar favoritos de usuário com persistência em MySQL. O projeto foi estruturado para mostrar uma stack tradicional bem organizada, com foco em clareza, segurança básica e apresentação de portfólio.

## Funcionalidades

- Busca de Digimons por nome, nível e tipo.
- Combinação de filtros na mesma pesquisa.
- Paginação dos resultados retornados pela API.
- Página interna de detalhes do Digimon com consumo da API externa.
- Prioridade para descrição em português, com fallback quando a tradução não estiver disponível.
- Cadastro e login de usuários.
- Senhas armazenadas com `password_hash()` e validadas com `password_verify()`.
- Regeneração de sessão após login.
- Logout com encerramento completo da sessão.
- Área restrita com proteção de rota.
- Favoritar e remover favoritos de forma individual.
- Listagem dos favoritos do próprio usuário autenticado.
- Cache local em arquivo para respostas da API, com TTL configurável.
- Configuração por ambiente via `.env`.
- Feedback visual com toasts, skeleton loading e microanimações.

## O que este projeto não inclui

- Testes automatizados.
- Painel administrativo.
- Framework backend como Laravel ou Symfony.
- Integração com banco em nuvem ou fila de processamento.

## Stack

- PHP sem framework
- MySQL
- Bootstrap 5
- Bootstrap Icons

## Estrutura

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
│   ├── digimon.php
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
│   ├── DigimonMapper.php
│   ├── FavoriteService.php
│   ├── Logger.php
│   ├── helpers.php
│   └── search.php
├── storage/
│   ├── cache/
│   └── logs/
├── templates/
│   ├── footer.php
│   ├── header.php
│   ├── search_results.php
│   └── ...
├── .env.example
└── .gitignore
```

## Como executar localmente

- Clone o repositório.

```bash
git clone [<url-do-repositorio>](https://github.com/AndersonCav/Digimon)
cd Digimon
```

- Crie o arquivo de ambiente.

```bash
copy .env.example .env
```

No PowerShell:

```powershell
Copy-Item .env.example .env
```

- Ajuste o arquivo `.env` com os dados do seu ambiente local.

- Crie o banco e as tabelas.

```bash
mysql -u root -p < database/schema.sql
```

- Se quiser dados fictícios de exemplo, importe o seed.

```bash
mysql -u root -p < database/seed_example.sql
```

- Execute no Apache/XAMPP e acesse:

```text
http://localhost/Digimon/public/index.php
```

## Variáveis de ambiente

```dotenv
APP_ENV=local
LOG_ENABLED=1
SESSION_SECURE_COOKIE=0
SESSION_SAMESITE=Lax

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=digimon
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4

DIGIMON_API_URL=https://digi-api.com/api/v1/digimon
CACHE_ENABLED=1
CACHE_TTL_SECONDS=300
```

## Segurança e higiene técnica

- Prepared statements em todas as consultas SQL relevantes.
- Escape de saída HTML com helper central.
- Token CSRF em formulários de autenticação e favoritos.
- Cookie de sessão endurecido com `HttpOnly`, `SameSite` e strict mode.
- Logout com limpeza completa de sessão e cookie.
- Logging interno em `storage/logs/app.log`.
- Cache local em `storage/cache` com limpeza previsível.

## Observações

Este é um projeto de estudo e portfólio. A intenção aqui foi manter a stack simples, mas com organização e acabamento suficientes para apresentar boas práticas de PHP tradicional sem recorrer a framework.
