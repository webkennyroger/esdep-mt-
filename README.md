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
- **30 testes** automatizados

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

## Deploy no Vercel

1. Import o repositorio no Vercel
2. Configure as environment variables:

| Variavel | Valor |
|---|---|
| `APP_KEY` | `base64:w7gKqaVQ3sVr9y/Np/b16QjhX8YQ02+kEem0aIEGwhU=` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL do deploy |
| `DB_CONNECTION` | `sqlite` |
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `database` |
| `MAIL_MAILER` | `log` |

## Credenciais Admin

- **Email:** admin@esdep.mt.gov.br
- **Senha:** password

## Estrutura

```
├── app/
│   ├── Actions/        # Logica de negocio
│   ├── Enums/          # Status enum
│   ├── Events/         # Eventos Laravel
│   ├── Jobs/           # Filas
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
└── tests/              # 30 testes
```

## Licenca

MIT
