<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use App\Models\ContactGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ContactManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterGroupId = null;
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;
    public bool $showImportModal = false;
    public bool $showGroupModal = false;
    public bool $showEditGroupModal = false;
    public bool $showDeleteGroupModal = false;
    public int $deleteGroupId = 0;
    public string $deleteGroupName = '';

    // Create contact
    public string $newName = '';
    public string $newEmail = '';
    public string $newCpf = '';
    public string $newTipo = '';
    public string $newSetor = '';
    public bool $newAutorizado = false;
    public array $newGroupIds = [];

    // Edit contact
    public int $editContactId = 0;
    public string $editName = '';
    public string $editEmail = '';
    public string $editCpf = '';
    public string $editTipo = '';
    public string $editSetor = '';
    public bool $editAutorizado = false;
    public array $editGroupIds = [];

    // View contact
    public ?Contact $viewContact = null;

    // Group
    public string $groupName = '';
    public string $groupDescription = '';
    public string $groupColor = '#059669';

    public int $editGroupId = 0;
    public string $editGroupName = '';
    public string $editGroupDescription = '';
    public string $editGroupColor = '#059669';

    public string $importCsv = '';

    protected $listeners = [
        'contactCreated' => '$refresh',
        'contactDeleted' => '$refresh',
        'groupCreated' => '$refresh',
    ];

    // ─── Contact CRUD ────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset(['newName', 'newEmail', 'newCpf', 'newTipo', 'newSetor', 'newAutorizado', 'newGroupIds']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createContact(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|max:255|unique:contacts,email',
        ]);

        $contact = Contact::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'cpf' => $this->newCpf ?: null,
            'tipo' => $this->newTipo ?: null,
            'setor' => $this->newSetor ?: null,
            'autorizado_at' => $this->newAutorizado ? now() : null,
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        if (!empty($this->newGroupIds)) {
            $contact->groups()->sync($this->newGroupIds);
        }

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Contato criado com sucesso.' } }))");
        $this->dispatch('contactCreated');
        $this->closeCreateModal();
    }

    public function openEditModal(int $id): void
    {
        $contact = Contact::with('groups')->findOrFail($id);
        $this->editContactId = $contact->id;
        $this->editName = $contact->name;
        $this->editEmail = $contact->email;
        $this->editCpf = $contact->cpf ?? '';
        $this->editTipo = $contact->tipo ?? '';
        $this->editSetor = $contact->setor ?? '';
        $this->editAutorizado = $contact->autorizado_at !== null;
        $this->editGroupIds = $contact->groups->pluck('id')->toArray();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function updateContact(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:contacts,email,' . $this->editContactId,
        ]);

        $contact = Contact::findOrFail($this->editContactId);
        $contact->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'cpf' => $this->editCpf ?: null,
            'tipo' => $this->editTipo ?: null,
            'setor' => $this->editSetor ?: null,
            'autorizado_at' => $this->editAutorizado ? ($contact->autorizado_at ?? now()) : null,
        ]);

        $contact->groups()->sync($this->editGroupIds);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Contato atualizado com sucesso.' } }))");
        $this->dispatch('contactCreated');
        $this->closeEditModal();
    }

    public function openViewModal(int $id): void
    {
        $this->viewContact = Contact::with('groups')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewContact = null;
    }

    public function toggleActive(int $id): void
    {
        $contact = Contact::findOrFail($id);

        if ($contact->is_active) {
            $contact->unsubscribe();
        } else {
            $contact->subscribe();
        }

        $this->dispatch('contactCreated');
    }

    public function confirmDeleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $this->deleteGroupId = $contact->id;
        $this->deleteGroupName = $contact->name;
        $this->showDeleteGroupModal = true;
    }

    public function deleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->groups()->detach();
        $contact->delete();

        $this->showDeleteGroupModal = false;
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Contato removido com sucesso.' } }))");
        $this->dispatch('contactDeleted');
    }

    // ─── Group CRUD ──────────────────────────────────

    public function openGroupModal(): void
    {
        $this->reset(['groupName', 'groupDescription', 'groupColor']);
        $this->groupColor = '#059669';
        $this->showGroupModal = true;
    }

    public function closeGroupModal(): void
    {
        $this->showGroupModal = false;
    }

    public function createGroup(): void
    {
        $this->validate([
            'groupName' => 'required|string|max:255|unique:contact_groups,name',
            'groupColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        ContactGroup::create([
            'name' => $this->groupName,
            'description' => $this->groupDescription,
            'color' => $this->groupColor,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Grupo criado com sucesso.' } }))");
        $this->dispatch('groupCreated');
        $this->closeGroupModal();
    }

    public function updatedGroupName(): void
    {
        $this->groupName = strtoupper($this->groupName);
    }

    public function updatedEditGroupName(): void
    {
        $this->editGroupName = strtoupper($this->editGroupName);
    }

    public function openEditGroupModal(int $id): void
    {
        $group = ContactGroup::findOrFail($id);
        $this->editGroupId = $group->id;
        $this->editGroupName = $group->name;
        $this->editGroupDescription = $group->description ?? '';
        $this->editGroupColor = $group->color;
        $this->showEditGroupModal = true;
    }

    public function closeEditGroupModal(): void
    {
        $this->showEditGroupModal = false;
    }

    public function updateGroup(): void
    {
        $this->validate([
            'editGroupName' => 'required|string|max:255|unique:contact_groups,name,' . $this->editGroupId,
            'editGroupColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $group = ContactGroup::findOrFail($this->editGroupId);
        $group->update([
            'name' => $this->editGroupName,
            'description' => $this->editGroupDescription,
            'color' => $this->editGroupColor,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Grupo atualizado com sucesso.' } }))");
        $this->dispatch('groupCreated');
        $this->closeEditGroupModal();
    }

    public function confirmDeleteGroup(int $id): void
    {
        $group = ContactGroup::findOrFail($id);
        $this->deleteGroupId = $group->id;
        $this->deleteGroupName = $group->name;
        $this->showDeleteGroupModal = true;
    }

    public function closeDeleteGroupModal(): void
    {
        $this->showDeleteGroupModal = false;
    }

    public function deleteGroup(int $id): void
    {
        $group = ContactGroup::findOrFail($id);
        $group->contacts()->detach();
        $group->delete();

        if ($this->filterGroupId === $id) {
            $this->filterGroupId = null;
        }

        $this->showDeleteGroupModal = false;
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Grupo excluido com sucesso.' } }))");
        $this->dispatch('groupCreated');
    }

    // ─── Filters ─────────────────────────────────────

    public function setFilterGroup(?int $groupId): void
    {
        $this->filterGroupId = ($this->filterGroupId === $groupId) ? null : $groupId;
        $this->resetPage();
    }

    // ─── Import ──────────────────────────────────────

    public function openImportModal(): void
    {
        $this->importCsv = '';
        $this->showImportModal = true;
    }

    public function importContacts(): void
    {
        $this->validate([
            'importCsv' => 'required|string',
        ]);

        $lines = explode("\n", $this->importCsv);
        $imported = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = str_getcsv($line);
            if (count($parts) < 2) continue;

            $name = trim($parts[0]);
            $email = trim($parts[1]);

            if (empty($name) || empty($email)) continue;

            $exists = Contact::where('email', $email)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            Contact::create([
                'name' => $name,
                'email' => $email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);

            $imported++;
        }

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '{$imported} contatos importados. {$skipped} ignorados (ja existentes).' } }))");
        $this->dispatch('contactCreated');
        $this->showImportModal = false;
        $this->importCsv = '';
    }

    // ─── Render ──────────────────────────────────────

    public function render()
    {
        $query = Contact::with('groups')->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('cpf', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterGroupId) {
            $query->whereHas('groups', fn($q) => $q->where('contact_groups.id', $this->filterGroupId));
        }

        return view('livewire.admin.contact-manager', [
            'contacts' => $query->paginate(15),
            'groups' => ContactGroup::withCount('contacts')->orderBy('name')->get(),
            'sectors' => ['CDI', 'DGE', 'DJ', 'DGDI', 'OUVIDORIA', 'INTITUCIONAL'],
            'tipos' => ['Defensor', 'Servidor', 'Estagiario', 'Gestor', 'Convidado'],
        ]);
    }
}
