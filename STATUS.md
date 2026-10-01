# Status — Redesign das páginas admin (para continuar de onde paramos)

> Arquivo de trabalho, não é documentação definitiva. Depois que essa tarefa
> terminar, pode apagar ou resumir o que for relevante para o `AGENTS.md`.

## Contexto da tarefa

O usuário mandou 7 prints/mockups de referência (Histórico de Atividades,
Relatórios, Nova Reserva, Agenda, Salas & Espaços, Contatos, Usuários &
Acessos) e pediu para redesenhar TODAS as páginas do admin para bater com
esses mockups — cores, posições, layout — **com uma exceção explícita: não
adicionar avatar/nome do usuário logado no topo de nenhuma página** (isso já
existe na sidebar, `resources/views/components/layouts/app.blade.php`, e deve
continuar só lá).

Padrão visual estabelecido em todas as páginas: ícone colorido
(`bg-green-700 dark:bg-[#00a477]`, `rounded-xl`, `h-11 w-11`) ao lado do
título + subtítulo, no canto superior esquerdo do cabeçalho.

## O que já foi feito (concluído e testado)

- **Cabeçalho com ícone** adicionado em: `room-manager.blade.php`,
  `user-manager.blade.php`, `contact-manager.blade.php`,
  `admin-calendar.blade.php`, `reports.blade.php`, `activity-log.blade.php`.
- **Reports.blade.php**: já tinha os gráficos de rosca ("Distribuição no
  período" com `$pieSegments`) — não precisou de agente, só o ícone do
  cabeçalho.
- **Reservation Manager** (`reservation-manager.blade.php` +
  `ReservationManager.php`): redesenho completo já feito em turnos
  anteriores — cabeçalho com busca/exportar/nova reserva numa linha, pills de
  filtro com contador (`filterCounts` no PHP), ícones nas colunas da tabela,
  ponto colorido no status, chips de serviço em grid 2 colunas (tabela E
  modal de detalhes).
- **Agenda** (`AdminCalendar.php` + `ContactCalendar.php`, view compartilhada
  `admin-calendar.blade.php`):
  - Botão "Salvar no Google" / baixar `.ics` por reserva (usa
    `IcsCalendarGenerator::googleCalendarUrl()`, novo método).
  - Card do dia redesenhado: painel de data com corte diagonal
    (`clip-path`), 4 badges circulares (Início/Término/Sala/Solicitante).
  - Legenda "Com reserva/Sem reserva/Hoje" virou pills.
  - Dropdown de sala (`roomId` novo, com `updatedRoomId()`) + botão "+ Nova
    Reserva" (escondido pro guard `contact`) adicionados nos dois
    componentes.
- **Correção de bug real**: `whereBetween('date', ...)` trocado por
  `whereDate()` em `Reports.php`/`ContactCalendar.php`/`ContactReports.php`
  (não afeta Postgres, mas afeta SQLite/testes locais).
- **Correção de bug real**: `exportExcel()`/`exportPdf()` em `Reports.php`
  agora usam `response()->streamDownload()` em vez de `response($bytes,...)`
  — sem isso o Livewire tentava colocar bytes binários no payload JSON e
  quebrava com "Malformed UTF-8 characters".
- **Papel "gerenciador" removido** (Spatie role) — só o cargo "Gerente" de
  Contact continua existindo, são conceitos diferentes.
- Botão "Voltar para o início" nas páginas de erro agora respeita o guard
  logado (`dashboard` pro admin, `contact.dashboard` pro contato, `home` pro
  visitante) em vez de sempre ir pro site público.

## Concluído nesta sessão (agentes em background — feito e verificado)

Dois agentes foram disparados para redesenhar páginas mais complexas. Os dois
terminaram, o site foi rebuildado (ver "Armadilhas" item 1) e testado — sem
erros:

1. **Histórico de Atividades** — `resources/views/livewire/admin/activity-log.blade.php`
   + `app/Livewire/Admin/ActivityLogController.php`. Feito: 4 cards com
   indicador de tendência (seta + %), filtros de Período/Tipo/Usuário no
   cabeçalho, busca + pills de ação com contador ("Todos", "Criações",
   "Atualizações", "Aprovações", "Rejeições", "Exclusões"), tabela com badge
   de ação azul, avatar do usuário, ícones de data/hora.
2. **Nova Reserva** — `resources/views/livewire/admin/reservation-editor.blade.php`
   (só a view; `ReservationEditor.php` não foi tocado, todo `wire:model`/
   validação preservado). Feito: continua como MODAL, mas com visual de
   página cheia dentro dele — modal alargado para `max-w-5xl`, cabeçalho com
   ícone, formulário em seções (Dados do responsável / Sala e horário /
   Descrição / Serviços / Anexos), sala agora é grid de cards clicáveis com
   bolinha de cor (antes era carrossel), coluna lateral com resumo dinâmico
   da reserva (Data/Horário/Sala/Responsável/Status).
   Verificado: as 6 classes Tailwind novas (`max-w-5xl`, `lg:col-span-2`,
   `bg-[#d6e1dc]`, `bg-[#eef7f2]`, `text-[#3d5a4e]`, `space-y-2.5`)
   compilaram certinho no CSS final.

Smoke test rodado via tinker (`Livewire::test(...)->html()`) autenticado como
super-admin nos 8 componentes admin (ReservationManager, AdminCalendar,
RoomManager, ContactManager, UserManager, ActivityLogController, Reports,
ReservationEditor) — todos renderizam sem erro.

## Ainda não iniciado

- **Salas & Espaços** (`room-manager.blade.php` + `RoomManager.php`): a
  tabela já está ~95% igual ao mockup (nome+cor, capacidade, grupo, cor do
  calendário, reservas, status, ações). Falta: mostrar o campo `code` da
  sala (existe no model, não é exibido) como linha pequena sob o nome;
  adicionar dropdowns de filtro "Todos os grupos" / "Todos os status" ao
  lado da busca (hoje só tem busca, sem esses dois filtros).
- **Usuários & Acessos** (`user-manager.blade.php` + `UserManager.php`):
  cabeçalho com ícone já feito; falta comparar a tabela de usuários ATIVOS
  com o mockup (colunas Pessoa/E-mail/Origem/Último acesso/Perfil/Status/
  Ações — conferir se já bate ou precisa de ajuste de ícones/badges) e
  adicionar dropdowns de filtro "Todos os perfis" / "Todos os status".
- **Contatos** (`contact-manager.blade.php`): cabeçalho com ícone já feito;
  a tabela já está bem parecida com o mockup (CPF/Nome, E-mail, Grupos,
  Cargo, Ações) — só confirmar se falta alguma coisa visual específica.

## Armadilhas descobertas nesta sessão (importante!)

1. **NUNCA rebuildar só um serviço do Docker Compose isoladamente.** O
   `compose.yaml` tem 4 serviços (`app`, `worker`, `scheduler`, `web`), e o
   `web` (nginx) copia `public/build` da imagem do `app` **no momento do
   build**, não em runtime. Se você rodar
   `docker compose build --no-cache app` (só um serviço) e depois só
   `docker compose up -d`, o `web` continua com a imagem antiga — HTML novo
   referenciando um CSS com hash novo, mas o nginx só tem o CSS com hash
   antigo em disco → **404 no CSS → site carrega sem nenhum estilo**. Sempre
   rode `docker compose build` (sem especificar serviço) ou
   `docker compose up --build -d` (que builda todos os serviços do arquivo).
2. **Verificar se uma classe Tailwind foi REALMENTE compilada exige grep
   preciso.** O CSS é minificado numa linha só; `grep -c "grid-cols-2"` dá
   falso positivo porque casa como substring de `sm:grid-cols-2` também. Use
   `grep -o '\.grid-cols-2{[^}]*}'` (com o ponto e a chave de abertura) para
   confirmar que a classe EXATA existe, sem prefixo de variante.
3. ~~Toda classe Tailwind nova só aparece no CSS depois de um rebuild
   completo.~~ **Resolvido**: existe agora um serviço `vite` no
   `compose.yaml` rodando `vite build --watch` continuamente, com
   `./resources` e `./public/build` como bind mount compartilhado com `app` e
   `web` (que também passaram a montar `./public/build` em vez de depender da
   cópia feita em build-time). Qualquer classe Tailwind nova usada num
   arquivo já existente recompila sozinha (confirmado: ~0,3–2s). O único caso
   que NÃO é pego automaticamente é criar um **arquivo .blade.php novo** — o
   watcher só redescobre arquivos novos na recompilação seguinte (editar
   qualquer arquivo já existente, mesmo que outro, destrava). Pra ver os
   rebuilds: `docker compose logs vite -f`.
   **Atualização**: o watcher também pode **parar de reagir silenciosamente**
   depois de rodar um tempo (container continua "Up", mas nenhum build novo
   acontece mesmo editando arquivos que antes disparavam rebuild na hora —
   aconteceu nesta sessão após ~40min). Sintoma: `docker compose logs vite
   --since 10s` fica vazio depois de salvar um arquivo. Fix: `docker compose
   restart vite` (não precisa rebuildar a imagem, só reiniciar o processo).
   Se uma classe nova não aparecer no CSS mesmo esperando, teste isso antes
   de desconfiar de outra coisa.
4. Este diretório é **filesystem compartilhado** com outra sessão rodando em
   paralelo (Windows, via Herd) que também edita esses arquivos. Sempre
   reconferir o estado atual de um arquivo antes de assumir que uma edição
   anterior ainda está lá.
5. `database/migrations` e `database/seeders` estão no bind mount ao vivo
   (adicionado nesta sessão) — migrations novas só rodam depois de
   `docker exec projeto_agora-app-1 php artisan migrate --force`.
6. **Aconteceu de verdade nesta sessão** (não é só teoria do item 1): rodei
   `docker compose build --no-cache app` sozinho enquanto os dois agentes
   ainda editavam arquivo — o site caiu para "zero CSS" (HTML sem nenhum
   estilo) porque `web`/`worker`/`scheduler` ficaram com o CSS antigo. Corrigido
   rodando **um único** `docker compose build` (todos os serviços, sem
   `--no-cache`, depois que os agentes terminaram de editar) + `up -d`.
   Confirmado resolvido: `curl` no CSS deu 200 (não 404) e os 8 componentes
   admin renderizaram sem erro via tinker. **Nota**: o site local roda em
   `http://localhost:8081` (porta mapeada no `compose.yaml`), não na porta 80 —
   testar na porta 80 dá 404 de um Apache do host, não do container.
