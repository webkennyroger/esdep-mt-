<?php

namespace App\Livewire\Admin;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class UserProfile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    public bool $showDeleteModal = false;

    public bool $isSsoUser = false;

    public bool $canChangeLocalPassword = true;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->isSsoUser = $user->keycloak_id !== null;
        // Login por senha local so funciona para super-admin (break-glass); para
        // os demais usuarios SSO, trocar a senha aqui nao teria efeito nenhum.
        $this->canChangeLocalPassword = ! $this->isSsoUser || $user->isSuperAdmin();
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.auth()->id(),
        ]);

        $user = auth()->user();
        $oldValues = $user->only(['name', 'email']);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        ActivityLogger::userUpdated($user, $oldValues, [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Perfil atualizado!', message: 'Seus dados foram salvos com sucesso.' } }))");
    }

    public function updatePassword(): void
    {
        abort_unless($this->canChangeLocalPassword, 403);

        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8|same:newPasswordConfirmation',
        ]);

        $user = auth()->user();

        if (! $user->password || ! Hash::check($this->currentPassword, $user->password)) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Erro', message: 'A senha atual está incorreta.' } }))");

            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Senha atualizada!', message: 'Sua senha foi alterada com sucesso.' } }))");
    }

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function deleteAccount(): void
    {
        $user = auth()->user();

        $user->delete();
        ActivityLogger::userDeleted($user);
        auth()->logout();

        $this->js("window.location.href = '{{ route('login') }}'");
    }

    public function render()
    {
        return view('livewire.admin.user-profile');
    }
}
