# ESDEP-MT — Memoria do Projeto

## Stack
- Laravel 12.67 + Livewire 4.4 + Pest PHP 3.8
- PHP 8.4.8 / SQLite / Vite
- Tailwind CSS 4

## Decisoes Importantes

### 2028-08-18: Remocao do TallStackUI Toast
- **Problema**: TallStackUI conflitava com CSS do projeto (cores, bordas, etc.)
- **Solucao**: Removido TallStackUI, criado componente Toast customizado com Alpine.js
- **Arquivo**: `resources/views/components/toast.blade.php`
- **Como funciona**: `$this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '...' } }))")` nos Livewire components

### 2028-08-18: Renomeacao apenas_projetor -> projetor
- Migration: `2026_08_18_105337_rename_apenas_projetor_to_projetor_in_reservations_table.php`
- Afeta: reservations table, Reservation model, CreateReservationAction, BookingForm, ReservationManager

### 2028-08-18: Descricao com rich text editor
- Toolbar com bold/italic/underline/list
- Alpine.js x-effect + @input para bind em contenteditable divs
- Aplicado em: reservation-editor.blade.php e booking-form.blade.php

### 2028-08-19: Termo de Compromisso e Regras de Uso
- **Problema**: Necessidade de registro formal de aceite das regras de uso dos espacos
- **Solucao**: Checkbox obrigatorio + modal com regras detalhadas
- **Arquivos**:
  - Migration: `2026_08_19_000002_add_terms_accepted_to_reservations_table.php`
  - Model: `Reservation.php` (campos `terms_accepted`, `terms_accepted_at`)
  - Component: `BookingForm.php` (validacao `accepted`, metodos `openTermsModal`, `acceptTerms`)
  - View: `booking-form.blade.php` (checkbox + modal com 5 secoes de regras)
  - Action: `CreateReservationAction.php` (salva dados de aceite)
- **Regras incluidas**:
  1. Conservacao de equipamentos
  2. Cancelamento e remarcacao (24h antecedencia)
  3. Pontualidade e uso do espaco
  4. Limpeza e organizacao
  5. Coffee break (48h antecedencia)

### 2028-08-19: Historico de Atividades
- **Problema**: Necessidade de registro completo de todas as acoes no sistema
- **Solucao**: Tabela `activity_logs` generica + servico `ActivityLogger` + pagina `/admin/activity`
- **Arquivos**:
  - Migration: `2026_08_19_100000_create_activity_logs_table.php`
  - Model: `app/Models/ActivityLog.php`
  - Service: `app/Services/ActivityLogger.php`
  - Component: `app/Livewire/Admin/ActivityLogController.php`
  - View: `resources/views/livewire/admin/activity-log.blade.php`
  - Rota: `/admin/activity`
- **Acoes registradas**:
  - Reservas: created, updated, approved, rejected
  - Usuarios: created, updated, deleted
  - Salas: created, updated, deleted
- **Funcionalidades da pagina**:
  - Filtros por acao, tipo, data
  - Busca por descricao ou usuario
  - Estatisticas (total, hoje, semana, reservas)
  - Modal de detalhes com mudancas (antes/depois)

### 2028-08-19: Descricoes detalhadas no Historico (Edicoes de Reserva)
- **Problema**: Descricoes genericas ("Reserva X atualizada") nao mostravam o que realmente mudou
- **Solucao**: `ActivityLogger::reservationUpdated` compara valores antigos (formulario) com novos (modelo) e gera descricao detalhada
- **Exemplos**: `Reserva "Workshop": Nome do evento alterado de "Reuniao" para "Workshop"; Data alterada de "01/09/2026" para "15/09/2026"`
- **Campos suportados**: Nome, Descricao, Data, Horarios, Sala, Solicitante, Email, Participantes, Coffee break, Sonorizacao, Transmissao, Projetor

### 2028-08-19: Remocao do campo "Grupo" no formulario de reserva
- **Problema**: Campo "Grupo (opcional)" nao tinha proposito util para os usuarios
- **Solucao**: Removido campo, coluna e relacoes com `contact_group_id` nas reservas
- **Arquivos**: Migration `2026_08_19_160536_remove_contact_group_id_from_reservations_table.php`, `booking-form.blade.php`, `BookingForm.php`, `CreateReservationAction.php`, `Reservation.php`, `ActivityLogger.php`, `ContactDashboard.php`, `ContactReservationEdit.php`
- **Nota**: A gestao de grupos/contatos (tabela `contact_contact_group`, `ContactGroup`, `ContactManager`) continua existindo, mas nao esta mais vinculada as reservas.

### 2028-08-19: Login de Contatos + Edicao por E-mail
- **Problema**: Contatos so recebiam emails, nao conseguiam interagir com o sistema
- **Solucao**: Login de contatos + Dashboard + Edicao de reservas pelo proprio solicitante (por e-mail)
- **Arquivos**:
  - Migration: `2026_08_19_132955_add_is_responsible_to_contact_contact_group_table.php`
  - Migration: `2026_08_19_133003_add_password_to_contacts_table.php`
  - Config: `config/auth.php` (guard contact + provider contacts)
  - Controller: `app/Http/Auth/ContactLoginController.php`
  - View: `resources/views/auth/contact-login.blade.php`
  - Component: `app/Livewire/Contact/ContactDashboard.php`
  - Component: `app/Livewire/Contact/ContactReservationEdit.php`
  - View: `resources/views/livewire/contact/contact-dashboard.blade.php`
  - View: `resources/views/livewire/contact/contact-reservation-edit.blade.php`
  - Rota: `/contato/login`, `/contato/painel`, `/contato/reserva/{id}/editar`
- **Fluxo**:
  - Contatos recebem senha para login
  - Dashboard mostra reservas vinculadas ao e-mail do contato (campo `requester_email`)
  - Apenas o solicitante (dono do e-mail) pode editar a reserva

### 2028-08-19: Envio de Emails Sincrono + Anti-Spam
- **Problema**: Emails eram enfileirados (nao tempo real) e caiam no spam
- **Solucao**: Envio sincrono com Mail::send() + headers anti-spam
- **Arquivos**:
  - `app/Actions/SendEventAnnouncementAction.php` - envio sincrono
  - `app/Mail/EventAnnouncementMail.php` - headers anti-spam
  - `app/Mail/ReservationApprovedMail.php` - headers anti-spam
  - `app/Mail/ReservationRequestedMail.php` - headers anti-spam
  - `.env` - configuracao SMTP Mailtrap
- **Headers anti-spam**:
  - List-Unsubscribe
  - Precedence: bulk/transactional
  - X-Mailer: ESDEP-MT/1.0
  - Return-Path e Sender

### 2028-08-19: Template de Email Divulgar
- **Problema**: Template generico nao mostrava informacoes detalhadas do evento
- **Solucao**: Template identico ao modal de detalhes do admin
- **Arquivo**: `resources/views/mail/events/announcement.blade.php`
- **Informacoes exibidas**:
  - Header verde com nome do evento + status
  - Grid 2x2: Data/Horario, Sala, Solicitante, Publico Estimado
  - Servicos solicitados (chips coloridos)
  - Descricao do evento
  - Protocolo da reserva
  - Mensagem complementar (se digitada)

## Convencoes

### Toast Notifications
- Usar `$this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success'|'error', title: '...', message: '...' } }))")`
- NUNCA usar `$this->dispatch('toast', ...)` (nao bubbles para window)
- NUNCA usar `session()->flash('toast', ...)` (nao funciona em tempo real)

### Rotas
- Admin: `/dashboard` (NAO `/admin`)
- Sub-rotas admin: `/admin/reservations`, `/admin/calendar`, `/admin/contacts`
- Publico: `/reservar`

### UI
- Locale: pt_BR, todo texto em portugues
- Dark mode via Alpine.js + localStorage
- Cores ESDEP: #003f32 (verde escuro), #008b5c (verde medio), #4fd1a5 (verde claro)
