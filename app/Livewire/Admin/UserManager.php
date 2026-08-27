<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Libera (ou revoga) o acesso de contas provisionadas pelo SSO e define o papel
 * de cada operador. Quem entra pelo Keycloak nasce inativo e sem papel: esta e
 * a tela que transforma "identificado" em "autorizado".
 */
#[Layout('components.layouts.app')]
class UserManager extends Component
{
    use WithPagination;

    /**
     * Papeis atribuiveis pela interface. A chave vazia representa o usuario sem
     * papel algum — pode entrar, mas so enxerga o que for aberto a todos.
     */
    public const ASSIGNABLE_ROLES = [
        '' => 'Sem perfil',
        'admin' => 'Administrador',
        'super-admin' => 'Super administrador',
    ];

    public string $search = '';

    /** Papel escolhido para cada conta pendente, indexado pelo id do usuario. */
    public array $roleChoice = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('manage-users'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $user = User::findOrFail($id);
        $role = $this->validatedRole($id);

        $user->activate(auth()->user());
        $user->syncRoles($role === '' ? [] : [$role]);

        ActivityLogger::userUpdated(
            $user,
            ['is_active' => false],
            ['is_active' => true, 'perfil' => self::ASSIGNABLE_ROLES[$role]]
        );

        $this->toast('success', "Acesso de {$user->name} liberado.");
    }

    public function revoke(int $id): void
    {
        $user = User::findOrFail($id);

        // Sem isso, um super-admin distraido se tranca para fora do sistema.
        if ($user->id === auth()->id()) {
            $this->toast('error', 'Voce nao pode revogar o proprio acesso.');

            return;
        }

        $user->forceFill(['is_active' => false])->save();

        ActivityLogger::userUpdated($user, ['is_active' => true], ['is_active' => false]);

        $this->toast('success', "Acesso de {$user->name} revogado.");
    }

    public function changeRole(int $id): void
    {
        $user = User::findOrFail($id);
        $role = $this->validatedRole($id);
        $previous = $user->roles->pluck('name')->implode(', ') ?: 'Sem perfil';

        // Rebaixar a si mesmo removeria o acesso a esta propria tela.
        if ($user->id === auth()->id() && $role !== 'super-admin') {
            $this->toast('error', 'Voce nao pode remover o proprio perfil de super administrador.');

            return;
        }

        $user->syncRoles($role === '' ? [] : [$role]);

        ActivityLogger::userUpdated($user, ['perfil' => $previous], ['perfil' => self::ASSIGNABLE_ROLES[$role]]);

        $this->toast('success', "Perfil de {$user->name} atualizado.");
    }

    /**
     * Le a escolha feita na linha do usuario, recusando qualquer papel que nao
     * esteja na lista — o valor chega do navegador e nao merece confianca.
     */
    private function validatedRole(int $id): string
    {
        $role = (string) ($this->roleChoice[$id] ?? '');

        abort_unless(array_key_exists($role, self::ASSIGNABLE_ROLES), 422);

        return $role;
    }

    private function toast(string $type, string $message): void
    {
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: '{$type}', message: '{$message}' } }))");
    }

    public function render()
    {
        $pending = User::with('roles')->where('is_active', false)->orderBy('created_at')->get();

        $active = User::with('roles')
            ->where('is_active', true)
            ->when($this->search !== '', function ($query) {
                $term = '%'.strtolower($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(email) LIKE ?', [$term]);
                });
            })
            ->orderBy('name')
            ->paginate(15);

        // Pre-seleciona o papel atual de cada linha, sem sobrescrever o que o
        // usuario ja escolheu mas ainda nao salvou.
        foreach ($pending->concat($active->getCollection()) as $user) {
            $this->roleChoice[$user->id] ??= $user->roles->pluck('name')->first() ?? '';
        }

        return view('livewire.admin.user-manager', [
            'pending' => $pending,
            'users' => $active,
            'roles' => self::ASSIGNABLE_ROLES,
        ]);
    }
}
