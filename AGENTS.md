# ESDEP-MT — Memoria do Projeto

## Stack
- Laravel 12.66 + Livewire 4.4 + TallStackUI 4.0
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
