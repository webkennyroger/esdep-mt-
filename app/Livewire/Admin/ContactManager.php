<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Rules\MultipleEmails;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ContactManager extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('access-contacts'), 403);
    }

    public string $search = '';

    public ?int $filterGroupId = null;

    // Contact Modals
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteContactModal = false;

    public int $deleteContactId = 0;

    public string $deleteContactName = '';

    // Group Modals
    public bool $showGroupModal = false;

    public bool $showEditGroupModal = false;

    public bool $showDeleteGroupModal = false;

    public int $deleteGroupId = 0;

    public string $deleteGroupName = '';

    // Import Modal
    public bool $showImportModal = false;

    public string $importCsv = '';

    // Create contact fields
    public string $newName = '';

    public string $newEmail = '';

    public string $newPassword = '';

    public string $newCpf = '';

    public string $newTipo = '';

    public string $newSetor = '';

    /** @deprecated Mantido apenas para compatibilidade com chamadas antigas. */
    public array $newGroupIds = [];

    /** @deprecated Mantido apenas para compatibilidade com chamadas antigas. */
    public array $newResponsibleGroupIds = [];

    // Edit contact fields
    public int $editContactId = 0;

    public string $editName = '';

    public string $editEmail = '';

    public string $editPassword = '';

    public string $editCpf = '';

    public string $editTipo = '';

    public string $editSetor = '';

    /** @deprecated Mantido apenas para compatibilidade com chamadas antigas. */
    public array $editGroupIds = [];

    /** @deprecated Mantido apenas para compatibilidade com chamadas antigas. */
    public array $editResponsibleGroupIds = [];

    // View contact
    public ?Contact $viewContact = null;

    // Create Group fields
    public string $groupName = '';

    public string $groupDescription = '';

    public string $groupColor = '#059669';

    public array $groupRoomIds = [];

    public array $groupContactIds = [];

    public string $groupNotificationEmail = '';

    // Edit Group fields
    public int $editGroupId = 0;

    public string $editGroupName = '';

    public string $editGroupDescription = '';

    public string $editGroupColor = '#059669';

    public array $editGroupRoomIds = [];

    public array $editGroupContactIds = [];

    public string $editGroupNotificationEmail = '';

    protected $listeners = [
        'contactCreated' => '$refresh',
        'contactUpdated' => '$refresh',
        'contactDeleted' => '$refresh',
        'groupCreated' => '$refresh',
    ];

    // ─── Contact CRUD ────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset(['newName', 'newEmail', 'newPassword', 'newCpf', 'newTipo', 'newSetor', 'newGroupIds', 'newResponsibleGroupIds']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->resetValidation();
        $this->showCreateModal = false;
    }

    public function createContact(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => ['required', 'email', 'max:255', 'unique:contacts,email'],
            'newPassword' => 'nullable|string|min:8',
            'newSetor' => ['nullable', 'string', Rule::exists('contact_groups', 'name')->whereNull('owner_contact_id')],
            'newTipo' => ['nullable', 'string', Rule::in($this->allowedCargos())],
        ]);

        $contact = Contact::create([
            'name' => $this->newName,
            'email' => strtolower(trim($this->newEmail)),
            'password' => $this->newPassword ? bcrypt($this->newPassword) : null,
            'cpf' => $this->newCpf ?: null,
            'tipo' => $this->newTipo ?: null,
            'setor' => $this->newSetor ?: null,
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        if ($this->newSetor !== '') {
            $this->syncContactSector($contact, $this->newSetor, $this->newTipo === 'Gerente');
        } elseif ($this->newGroupIds !== []) {
            $this->syncLegacyGroups($contact, $this->newGroupIds, $this->newResponsibleGroupIds);
        }

        ActivityLogger::created($contact, "Contato \"{$contact->name}\" criado");

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
        $this->editPassword = '';
        $this->editCpf = $contact->cpf ?? '';
        $this->editTipo = $contact->tipo ?? '';
        $this->editSetor = $contact->setor ?? '';
        $globalGroupIds = ContactGroup::whereNull('owner_contact_id')->pluck('id');
        $this->editGroupIds = $contact->groups->pluck('id')->intersect($globalGroupIds)->values()->all();
        $this->editResponsibleGroupIds = $contact->groups->whereIn('id', $globalGroupIds)
            ->filter(fn ($group) => (bool) $group->getRelation('pivot')->getAttribute('is_responsible'))->pluck('id')->all();
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->resetValidation();
        $this->showEditModal = false;
    }

    public function updateContact(): void
    {
        $contact = Contact::findOrFail($this->editContactId);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => ['required', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($this->editContactId)],
            'editPassword' => 'nullable|string|min:8',
            'editSetor' => ['nullable', 'string', Rule::exists('contact_groups', 'name')->whereNull('owner_contact_id')],
            'editTipo' => ['nullable', 'string', Rule::in($this->allowedCargos())],
        ]);

        $oldValues = $contact->toArray();

        $data = [
            'name' => $this->editName,
            'email' => strtolower(trim($this->editEmail)),
            'cpf' => $this->editCpf ?: null,
            'tipo' => $this->editTipo ?: null,
            'setor' => $this->editSetor ?: null,
        ];

        if ($this->editPassword) {
            $data['password'] = bcrypt($this->editPassword);
        }

        $legacyGroupsChanged = $this->editSetor === '' && ($this->editGroupIds !== [] || $contact->groups->whereNull('owner_contact_id')->isNotEmpty());
        DB::transaction(function () use ($contact, $data, $legacyGroupsChanged): void {
            $contact->update($data);
            if ($this->editSetor !== '') {
                $this->syncContactSector($contact, $this->editSetor, $this->editTipo === 'Gerente');
            } elseif ($legacyGroupsChanged) {
                $this->syncLegacyGroups($contact, $this->editGroupIds, $this->editResponsibleGroupIds);
            }
        });

        ActivityLogger::updated($contact, $oldValues, $contact->fresh()->toArray(), "Contato \"{$contact->name}\" atualizado");

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Contato atualizado com sucesso.' } }))");
        $this->dispatch('contactUpdated');
        $this->closeEditModal();
    }

    private function canManageAdmins(): bool
    {
        return (bool) auth('web')->user()?->can('manage-users');
    }

    /**
     * Cargo e so informativo — nao concede nenhum acesso ou permissao.
     */
    private function allowedCargos(): array
    {
        return ['Assessor', 'Defensor', 'Diretor', 'Coordenador', 'Gerente', 'Estagiario'];
    }

    private function syncContactSector(Contact $contact, ?string $sector, bool $isResponsible = false): void
    {
        $globalGroups = ContactGroup::whereNull('owner_contact_id')->get(['id', 'name']);
        $contact->groups()->detach($globalGroups->pluck('id')->all());

        if ($sector === null || $sector === '') {
            return;
        }

        $group = $globalGroups->firstWhere('name', $sector);
        if ($group) {
            $contact->groups()->syncWithoutDetaching([$group->id => ['is_responsible' => $isResponsible]]);
        }
    }

    protected function syncLegacyGroups(Contact $contact, array $groupIds, array $responsibleIds = []): void
    {
        $globalGroupIds = ContactGroup::whereNull('owner_contact_id')->pluck('id');
        $selected = collect($groupIds)->map(fn ($id) => (int) $id)->intersect($globalGroupIds);
        $contact->groups()->detach($globalGroupIds->all());
        $contact->groups()->syncWithoutDetaching($selected->mapWithKeys(fn ($id) => [
            $id => ['is_responsible' => in_array($id, $responsibleIds)],
        ])->all());
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

    public function confirmDeleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $this->deleteContactId = $contact->id;
        $this->deleteContactName = $contact->name;
        $this->showDeleteContactModal = true;
    }

    public function closeDeleteContactModal(): void
    {
        $this->showDeleteContactModal = false;
        $this->deleteContactId = 0;
        $this->deleteContactName = '';
    }

    public function deleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);

        abort_if($contact->user_id && ! $this->canManageAdmins(), 403);

        if (ContactGroup::where('owner_contact_id', $contact->id)->exists()) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Não é possível excluir um contato responsável por um grupo. Transfira ou exclua o grupo primeiro.' } }))");
            $this->closeDeleteContactModal();

            return;
        }
        if ($contact->user_id && $contact->user_id === auth()->id()) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Você não pode excluir sua própria conta por aqui.' } }))");
            $this->closeDeleteContactModal();

            return;
        }
        $name = $contact->name;
        $contact->groups()->detach();
        if ($contact->user_id) {
            User::whereKey($contact->user_id)->delete();
        }
        $contact->delete();

        ActivityLogger::log('deleted', "Contato \"{$name}\" excluído", null, ['name' => $name, 'id' => $id]);
        $this->closeDeleteContactModal();
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Contato removido com sucesso.' } }))");
        $this->dispatch('contactDeleted');
    }

    // ─── Group CRUD ──────────────────────────────────

    public function openGroupModal(): void
    {
        $this->reset(['groupName', 'groupDescription', 'groupColor', 'groupRoomIds', 'groupContactIds', 'groupNotificationEmail']);
        $this->groupColor = '#059669';
        $this->resetValidation();
        $this->showGroupModal = true;
    }

    public function closeGroupModal(): void
    {
        $this->resetValidation();
        $this->showGroupModal = false;
    }

    public function createGroup(): void
    {
        $this->validate([
            'groupName' => 'required|string|max:255|unique:contact_groups,name',
            'groupColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'groupRoomIds' => 'array',
            'groupRoomIds.*' => 'integer|exists:rooms,id',
            'groupContactIds' => 'array',
            'groupContactIds.*' => 'integer|exists:contacts,id',
            'groupNotificationEmail' => ['nullable', 'string', 'max:255', new MultipleEmails],
        ]);

        $group = ContactGroup::create([
            'name' => $this->groupName,
            'description' => $this->groupDescription,
            'color' => $this->groupColor,
            'notification_email' => $this->groupNotificationEmail ?: null,
        ]);
        Room::whereIn('id', $this->groupRoomIds)->update(['contact_group_id' => $group->id]);
        $group->contacts()->attach($this->groupContactIds);

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
        $group = ContactGroup::whereNull('owner_contact_id')->with(['rooms', 'contacts'])->findOrFail($id);
        $this->editGroupId = $group->id;
        $this->editGroupName = $group->name;
        $this->editGroupDescription = $group->description ?? '';
        $this->editGroupColor = $group->color;
        $this->editGroupRoomIds = $group->rooms->pluck('id')->all();
        $this->editGroupContactIds = $group->contacts->pluck('id')->all();
        $this->editGroupNotificationEmail = $group->notification_email ?? '';
        $this->resetValidation();
        $this->showEditGroupModal = true;
    }

    public function closeEditGroupModal(): void
    {
        $this->resetValidation();
        $this->showEditGroupModal = false;
    }

    public function updateGroup(): void
    {
        $group = ContactGroup::whereNull('owner_contact_id')->findOrFail($this->editGroupId);
        $this->validate([
            'editGroupName' => 'required|string|max:255|unique:contact_groups,name,'.$this->editGroupId,
            'editGroupColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'editGroupRoomIds' => 'array',
            'editGroupRoomIds.*' => 'integer|exists:rooms,id',
            'editGroupContactIds' => 'array',
            'editGroupContactIds.*' => 'integer|exists:contacts,id',
            'editGroupNotificationEmail' => ['nullable', 'string', 'max:255', new MultipleEmails],
        ]);

        DB::transaction(function () use ($group): void {
            $group->update([
                'name' => $this->editGroupName,
                'description' => $this->editGroupDescription,
                'color' => $this->editGroupColor,
                'notification_email' => $this->editGroupNotificationEmail ?: null,
            ]);
            Room::where('contact_group_id', $group->id)
                ->whereNotIn('id', $this->editGroupRoomIds)
                ->update(['contact_group_id' => null]);
            Room::whereIn('id', $this->editGroupRoomIds)->update(['contact_group_id' => $group->id]);

            $this->syncGroupContacts($group, $this->editGroupContactIds);
        });

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Grupo atualizado com sucesso.' } }))");
        $this->dispatch('groupCreated');
        $this->closeEditGroupModal();
    }

    protected function syncGroupContacts(ContactGroup $group, array $contactIds): void
    {
        $existingResponsible = $group->contacts()->wherePivot('is_responsible', true)->pluck('contacts.id');
        $syncData = collect($contactIds)->mapWithKeys(fn ($id) => [
            $id => ['is_responsible' => $existingResponsible->contains($id)],
        ]);
        $group->contacts()->sync($syncData);
    }

    public function confirmDeleteGroup(int $id): void
    {
        $group = ContactGroup::whereNull('owner_contact_id')->findOrFail($id);
        $this->deleteGroupId = $group->id;
        $this->deleteGroupName = $group->name;
        $this->showDeleteGroupModal = true;
    }

    public function closeDeleteGroupModal(): void
    {
        $this->showDeleteGroupModal = false;
        $this->deleteGroupId = 0;
        $this->deleteGroupName = '';
    }

    public function deleteGroup(int $id): void
    {
        $group = ContactGroup::whereNull('owner_contact_id')->findOrFail($id);
        $group->contacts()->detach();
        $group->delete();

        if ($this->filterGroupId === $id) {
            $this->filterGroupId = null;
        }

        $this->closeDeleteGroupModal();
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Grupo excluído com sucesso.' } }))");
        $this->dispatch('groupCreated');
    }

    // ─── Filters ─────────────────────────────────────

    public function setFilterGroup(?int $groupId): void
    {
        $this->filterGroupId = ($this->filterGroupId === $groupId) ? null : $groupId;
        $this->resetPage();
    }

    public function reorderGroups(array $ids): void
    {
        $allowed = ContactGroup::whereNull('owner_contact_id')->whereIn('id', $ids)->pluck('id');
        abort_if($allowed->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            ContactGroup::whereNull('owner_contact_id')->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    public function reorderContacts(array $ids): void
    {
        abort_if(Contact::whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            Contact::whereKey($id)->update(['sort_order' => $position]);
        }
    }

    // ─── Import ──────────────────────────────────────

    public function openImportModal(): void
    {
        $this->importCsv = '';
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importCsv = '';
    }

    public function importContacts(): void
    {
        $this->validate([
            'importCsv' => 'required|string|max:102400',
        ]);

        $lines = explode("\n", $this->importCsv);
        $imported = 0;
        $skipped = 0;
        $invalid = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = str_getcsv($line);
            if (count($parts) < 2) {
                continue;
            }

            $name = trim($parts[0]);
            $email = strtolower(trim($parts[1]));

            if (empty($name) || empty($email)) {
                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid++;

                continue;
            }

            if (Contact::where('email', $email)->exists()) {
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

        $msg = "{$imported} contatos importados. {$skipped} ignorados (já existentes).";
        if ($invalid > 0) {
            $msg .= " {$invalid} com e-mail inválido ignorados.";
        }

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '{$msg}' } }))");
        $this->dispatch('contactCreated');
        $this->closeImportModal();
    }

    // ─── Render ──────────────────────────────────────

    public function render()
    {
        $query = Contact::with('groups')
            ->addSelect([
                'reservations_count' => Reservation::selectRaw('count(*)')
                    ->whereColumn('reservations.requester_email', 'contacts.email'),
            ])
            ->orderBy('sort_order')->orderBy('name');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('cpf', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterGroupId) {
            $query->whereHas('groups', fn ($q) => $q->where('contact_groups.id', $this->filterGroupId));
        }

        $groups = ContactGroup::whereNull('owner_contact_id')->withCount(['contacts', 'rooms'])->orderBy('sort_order')->orderBy('name')->get();
        $isSuperAdmin = (bool) auth('web')->user()?->isSuperAdmin();

        return view('livewire.admin.contact-manager', [
            'contacts' => $query->paginate(15),
            'groups' => $groups,
            'rooms' => Room::with('contactGroup')->where(fn ($rooms) => $rooms->whereNull('contact_group_id')->orWhereHas('contactGroup', fn ($groups) => $groups->whereNull('owner_contact_id')))->orderBy('name')->get(),
            'contactsForGroups' => Contact::orderBy('name')->limit(300)->get(['id', 'name', 'email']),
            'sectors' => $groups->pluck('name')->values()->all(),
            'cargos' => $this->allowedCargos(),
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }
}
