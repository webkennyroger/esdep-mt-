<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class UserProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public bool $showDeleteModal = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
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
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Erro', message: 'A senha atual esta incorreta.' } }))");
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
        
        ActivityLogger::userDeleted($user);

        auth()->logout();
        $user->delete();

        $this->js("window.location.href = '{{ route('login') }}'");
    }

    public function render()
    {
        return view('livewire.admin.user-profile');
    }
}
