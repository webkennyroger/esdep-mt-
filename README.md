# ESDEP-MT - Sistema de Agenda de Salas

Sistema de agendamento de salas da Escola Superior da Defensoria Publica do Estado de Mato Grosso.

## Funcionalidades

- **Formulario publico** de reservas com selecao de sala, data, horario e servicos
- **Painel admin** com gerenciamento completo de reservas (aprovar/rejeitar)
- **Timeline** de eventos autorizados com paginacao
- **Sistema de contatos** e grupos para divulgacao
- **Divulgacao de eventos** via email
- **Deteccao de conflito** de horarios
- **Modo escuro** com toggle
- Grupos de contatos vinculados às salas, com responsáveis e acesso isolado às reservas
- **57 testes** automatizados

## Tecnologias

- Laravel 12.x
- Livewire 4.4
- Tailwind CSS 4.0
- SQLite (dev) / MySQL (producao)

## Instalacao Local

```bash
# Clonar repositorio
git clone https://github.com/webkennyroger/esdep-mt-.git
cd esdep-mt-

# Instalar dependencias
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Criar banco e dados
php artisan migrate:fresh --seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

Acesse: http://localhost:8000

## Deploy com Docker

Crie o arquivo de ambiente sem versionar segredos:

```bash
cp .env.example .env
php artisan key:generate --show
```

Copie a chave gerada para `APP_KEY` no `.env` e configure, no mínimo, `APP_URL`, SMTP e cookies HTTPS. Nunca reutilize chaves ou senhas da documentação.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://agenda.exemplo.gov.br
SESSION_SECURE_COOKIE=true
MAIL_MAILER=smtp
```

Suba a aplicação:

```bash
docker compose up -d --build
docker compose ps
```

O container `app` executa migrations, cache de configuração e cache das views. O serviço `scheduler` executa os lembretes programados. O banco SQLite e o diretório `storage` ficam em volumes persistentes.

Antes de atualizar, faça backup do banco:

```bash
docker compose exec app php artisan down
docker compose cp app:/var/www/html/database/database.sqlite ./backup-database.sqlite
docker compose up -d --build
docker compose exec app php artisan up
```

Para produção com mais de uma réplica, troque SQLite por MySQL ou PostgreSQL e configure as variáveis `DB_*`.

### Variáveis essenciais

| Variavel | Valor |
|---|---|
| `APP_KEY` | Chave exclusiva gerada para o ambiente |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL do deploy |
| `DB_CONNECTION` | `sqlite` |
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `database` |
| `MAIL_MAILER` | `smtp` |

Não execute seeders de demonstração em produção. Crie o primeiro Super Admin com uma senha exclusiva e forte por um procedimento administrativo controlado.

## Estrutura

```
├── app/
│   ├── Actions/        # Logica de negocio
│   ├── Enums/          # Status enum
│   ├── Events/         # Eventos Laravel
│   ├── Livewire/       # Componentes
│   │   ├── Admin/      # Painel administrativo
│   │   └── Public/     # Site publico
│   ├── Mail/           # Emails
│   ├── Models/         # Modelos Eloquent
│   └── Services/       # Servicos
├── database/
│   ├── migrations/     # Migracoes
│   └── seeders/        # Dados iniciais
├── resources/views/    # Templates Blade
├── routes/             # Rotas
├── docker/             # PHP e Nginx para producao
└── tests/              # Testes automatizados
```

## Licenca

MIT
