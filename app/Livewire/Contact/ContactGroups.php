<?php

namespace App\Livewire\Contact;

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.contact', ['title' => 'Contatos e Grupos — ESDEP-MT'])]
class ContactGroups extends ContactManager
{
    public function mount(): void {}

    private ?Collection $allowedGroupIdsCache = null;

    private function allowedGroupIds(): Collection
    {
        if ($this->allowedGroupIdsCache !== null) {
            return $this->allowedGroupIdsCache;
        }

        $contactId = auth('contact')->id();

        return $this->allowedGroupIdsCache = ContactGroup::query()
            ->where(fn (Builder $groups) => $groups
                ->where('owner_contact_id', $contactId)
                ->orWhereHas('contacts', fn (Builder $contacts) => $contacts->whereKey($contactId)))
            ->pluck('id');
    }

    private function editableGroupIds(): Collection
    {
        return ContactGroup::where('owner_contact_id', auth('contact')->id())->pluck('id');
    }

    private function allowedContacts(): Builder
    {
        return Contact::query()->whereHas('groups', fn (Builder $groups) => $groups->whereIn('contact_groups.id', $this->allowedGroupIds()));
    }

    private function allowedContact(int $id): Contact
    {
        return Contact::with('groups')->whereKey($id)
            ->whereHas('groups', fn (Builder $groups) => $groups->whereIn('contact_groups.id', $this->allowedGroupIds()))
            ->firstOrFail();
    }

    private function allowedGroup(int $id): ContactGroup
    {
        return ContactGroup::whereIn('id', $this->allowedGroupIds())->findOrFail($id);
    }

    public function createContact(): void
    {
        $this->validate(['newName' => 'required|string|max:255', 'newEmail' => 'required|email|max:255|unique:contacts,email', 'newPassword' => 'nullable|string|min:8']);
        $contact = Contact::create(['name' => $this->newName, 'email' => strtolower(trim($this->newEmail)), 'password' => $this->newPassword ?: null, 'cpf' => $this->newCpf ?: null, 'tipo' => $this->newTipo ?: null, 'setor' => $this->newSetor ?: null, 'is_active' => true, 'subscribed_at' => now()]);
        $group = $this->ownedGroupForSector($this->newSetor);
        if ($group) {
            $contact->groups()->attach($group, ['is_responsible' => false]);
        } elseif ($this->newGroupIds !== []) {
            $this->syncPrivateGroups($contact, $this->newGroupIds, $this->newResponsibleGroupIds);
        }
        ActivityLogger::created($contact, "Contato \"{$contact->name}\" criado");
        $this->closeCreateModal();
        $this->toast('Contato criado com sucesso.');
    }

    public function openEditModal(int $id): void
    {
        $contact = $this->allowedContact($id);
        $allowed = $this->editableGroupIds();
        $this->editContactId = $contact->id;
        $this->editName = $contact->name;
        $this->editEmail = $contact->email;
        $this->editPassword = '';
        $this->editCpf = $contact->cpf ?? '';
        $this->editTipo = $contact->tipo ?? '';
        $this->editSetor = $contact->setor ?? '';
        $this->editGroupIds = $contact->groups->pluck('id')->intersect($allowed)->values()->all();
        $this->editResponsibleGroupIds = $contact->groups->whereIn('id', $allowed)
            ->filter(fn ($group) => (bool) $group->getRelation('pivot')->getAttribute('is_responsible'))->pluck('id')->all();
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function updateContact(): void
    {
        $contact = $this->allowedContact($this->editContactId);
        $this->validate(['editName' => 'required|string|max:255', 'editEmail' => ['required', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($contact->id)], 'editPassword' => 'nullable|string|min:8']);
        $old = $contact->toArray();
        $data = ['name' => $this->editName, 'email' => strtolower(trim($this->editEmail)), 'cpf' => $this->editCpf ?: null, 'tipo' => $this->editTipo ?: null, 'setor' => $this->editSetor ?: null];
        if ($this->editPassword !== '') {
            $data['password'] = $this->editPassword;
        }
        $allowed = $this->editableGroupIds();

        DB::transaction(function () use ($contact, $data, $allowed): void {
            $contact->update($data);
            if ($this->editSetor !== '') {
                $contact->groups()->detach($allowed->all());
                $group = $this->ownedGroupForSector($this->editSetor);
                if ($group) {
                    $contact->groups()->syncWithoutDetaching([$group->id => ['is_responsible' => false]]);
                }
            } elseif ($this->editGroupIds !== []) {
                $this->syncPrivateGroups($contact, $this->editGroupIds, $this->editResponsibleGroupIds);
            }
        });
        ActivityLogger::updated($contact, $old, $contact->fresh()->toArray(), "Contato \"{$contact->name}\" atualizado");
        $this->closeEditModal();
        $this->toast('Contato atualizado com sucesso.');
    }

    private function ownedGroupForSector(?string $sector): ?ContactGroup
    {
        if (! $sector) {
            return null;
        }

        return ContactGroup::where('owner_contact_id', auth('contact')->id())->where('name', $sector)->first();
    }

    private function syncPrivateGroups(Contact $contact, array $groupIds, array $responsibleIds = []): void
    {
        $editable = $this->editableGroupIds();
        $selected = collect($groupIds)->map(fn ($id) => (int) $id)->intersect($editable);
        $contact->groups()->detach($editable->all());
        $contact->groups()->syncWithoutDetaching($selected->mapWithKeys(fn ($id) => [
            $id => ['is_responsible' => in_array($id, $responsibleIds)],
        ])->all());
    }

    public function openViewModal(int $id): void
    {
        $this->viewContact = $this->allowedContact($id);
        $this->showViewModal = true;
    }

    public function toggleActive(int $id): void
    {
        $contact = $this->allowedContact($id);
        $contact->is_active ? $contact->unsubscribe() : $contact->subscribe();
        ActivityLogger::log('updated', "Contato \"{$contact->name}\" atualizado", $contact, ['is_active' => $contact->is_active]);
    }

    public function confirmDeleteContact(int $id): void
    {
        $contact = $this->allowedContact($id);
        $this->deleteContactId = $contact->id;
        $this->deleteContactName = $contact->name;
        $this->showDeleteContactModal = true;
    }

    public function deleteContact(int $id): void
    {
        abort_if($id === auth('contact')->id(), 403);
        $contact = $this->allowedContact($id);
        $allowed = $this->allowedGroupIds();
        $willBeOrphaned = ! $contact->groups()->whereNotIn('contact_groups.id', $allowed)->exists();

        if ($willBeOrphaned && Reservation::where('requester_email', strtolower($contact->email))->exists()) {
            $this->closeDeleteContactModal();
            $this->toast('Não é possível excluir um contato com histórico de reservas. Desative-o.', 'error');

            return;
        }

        if ($willBeOrphaned && ContactGroup::where('owner_contact_id', $contact->id)->exists()) {
            $this->closeDeleteContactModal();
            $this->toast('Não é possível excluir um contato responsável por um grupo. Exclua o grupo primeiro.', 'error');

            return;
        }

        $contact->groups()->detach($allowed);
        $name = $contact->name;
        if ($willBeOrphaned) {
            $contact->delete();
        }
        ActivityLogger::log('deleted', "Contato \"{$name}\" removido dos grupos administrados", null, ['name' => $name, 'id' => $id]);
        $this->closeDeleteContactModal();
        $this->toast('Contato removido com sucesso.');
    }

    public function createGroup(): void
    {
        $managedRoomIds = auth('contact')->user()->responsibleRoomIds()->pluck('id');
        abort_if(collect($this->groupRoomIds)->diff($managedRoomIds)->isNotEmpty(), 403);
        $allowedContactIds = $this->allowedContacts()->pluck('id');
        abort_if(collect($this->groupContactIds)->diff($allowedContactIds)->isNotEmpty(), 403);
        $this->validate(['groupName' => 'required|string|max:255|unique:contact_groups,name', 'groupColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']]);
        $group = ContactGroup::create(['owner_contact_id' => auth('contact')->id(), 'name' => $this->groupName, 'description' => $this->groupDescription, 'color' => $this->groupColor]);
        auth('contact')->user()->groups()->attach($group, ['is_responsible' => true]);
        Room::whereIn('id', $this->groupRoomIds)->update(['contact_group_id' => $group->id]);
        $group->contacts()->attach(collect($this->groupContactIds)->diff([auth('contact')->id()])->all());
        $this->allowedGroupIdsCache = null;
        $this->closeGroupModal();
        $this->toast('Grupo criado com sucesso.');
    }

    public function openEditGroupModal(int $id): void
    {
        $group = $this->allowedGroup($id)->load(['rooms', 'contacts']);
        $this->editGroupId = $group->id;
        $this->editGroupName = $group->name;
        $this->editGroupDescription = $group->description ?? '';
        $this->editGroupColor = $group->color;
        $this->editGroupRoomIds = $group->rooms->pluck('id')->all();
        $this->editGroupContactIds = $group->contacts->pluck('id')->all();
        $this->resetValidation();
        $this->showEditGroupModal = true;
    }

    public function updateGroup(): void
    {
        $group = $this->allowedGroup($this->editGroupId);
        $permittedRooms = Room::whereIn('contact_group_id', $this->allowedGroupIds())->pluck('id');
        abort_if(collect($this->editGroupRoomIds)->diff($permittedRooms)->isNotEmpty(), 403);
        $allowedContactIds = $this->allowedContacts()->pluck('id');
        abort_if(collect($this->editGroupContactIds)->diff($allowedContactIds)->isNotEmpty(), 403);
        $this->validate(['editGroupName' => ['required', 'string', 'max:255', Rule::unique('contact_groups', 'name')->ignore($group->id)], 'editGroupColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']]);
        DB::transaction(function () use ($group): void {
            $group->update(['name' => $this->editGroupName, 'description' => $this->editGroupDescription, 'color' => $this->editGroupColor]);
            Room::where('contact_group_id', $group->id)->whereNotIn('id', $this->editGroupRoomIds)->update(['contact_group_id' => null]);
            Room::whereIn('id', $this->editGroupRoomIds)->update(['contact_group_id' => $group->id]);
            $contactIds = collect($this->editGroupContactIds)->push($group->owner_contact_id)->filter()->unique()->all();
            $this->syncGroupContacts($group, $contactIds);
        });
        $this->closeEditGroupModal();
        $this->toast('Grupo atualizado com sucesso.');
    }

    public function confirmDeleteGroup(int $id): void
    {
        $group = $this->allowedGroup($id);
        $this->deleteGroupId = $group->id;
        $this->deleteGroupName = $group->name;
        $this->showDeleteGroupModal = true;
    }

    public function deleteGroup(int $id): void
    {
        $group = $this->allowedGroup($id);
        $group->contacts()->detach();
        $group->delete();
        $this->allowedGroupIdsCache = null;
        if ($this->filterGroupId === $id) {
            $this->filterGroupId = null;
        } $this->closeDeleteGroupModal();
        $this->toast('Grupo excluído com sucesso.');
    }

    public function setFilterGroup(?int $groupId): void
    {
        if ($groupId !== null) {
            $this->allowedGroup($groupId);
        } $this->filterGroupId = $this->filterGroupId === $groupId ? null : $groupId;
        $this->resetPage();
    }

    public function reorderGroups(array $ids): void
    {
        $uniqueIds = collect($ids)->map(fn ($id) => (int) $id)->unique()->values();
        $ownedIds = ContactGroup::where('owner_contact_id', auth('contact')->id())->whereIn('id', $uniqueIds)->pluck('id');
        abort_if($ownedIds->count() !== $uniqueIds->count(), 403);

        foreach ($uniqueIds as $position => $id) {
            ContactGroup::where('owner_contact_id', auth('contact')->id())->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    public function reorderContacts(array $ids): void
    {
        abort_if($this->allowedContacts()->whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            Contact::whereKey($id)->update(['sort_order' => $position]);
        }
    }

    public function importContacts(): void
    {
        $this->validate(['importCsv' => 'required|string|max:102400']);
        $groupId = $this->filterGroupId ?: $this->allowedGroupIds()->first();
        abort_if(! $groupId, 422);
        $imported = 0;
        $skipped = 0;
        foreach (explode("\n", $this->importCsv) as $line) {
            $parts = str_getcsv(trim($line));
            if (count($parts) < 2) {
                continue;
            }
            $name = trim($parts[0]);
            $email = strtolower(trim($parts[1]));
            if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;

                continue;
            }
            $contact = Contact::firstOrCreate(['email' => $email], ['name' => $name, 'is_active' => true, 'subscribed_at' => now()]);
            if ($contact->groups()->whereKeyNot($groupId)->exists() && ! $this->allowedContacts()->whereKey($contact->id)->exists()) {
                $skipped++;

                continue;
            }
            $contact->groups()->syncWithoutDetaching([$groupId => ['is_responsible' => false]]);
            $imported++;
        }
        $this->closeImportModal();
        $this->toast("{$imported} contatos importados. {$skipped} ignorados.");
    }

    private function toast(string $message, string $type = 'success'): void
    {
        $detail = json_encode(compact('type', 'message'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: {$detail} }))");
    }

    public function render()
    {
        if ($this->filterGroupId && ! $this->allowedGroupIds()->contains($this->filterGroupId)) {
            $this->filterGroupId = null;
            $this->resetPage();
        }

        $groups = ContactGroup::whereIn('id', $this->allowedGroupIds())->withCount(['contacts', 'rooms'])->orderBy('sort_order')->orderBy('name')->get();
        $query = $this->allowedContacts()->with('groups')
            ->addSelect([
                'reservations_count' => Reservation::selectRaw('count(*)')
                    ->whereColumn('reservations.requester_email', 'contacts.email'),
            ])
            ->orderBy('sort_order')->orderBy('name');
        if ($this->search !== '') {
            $query->where(fn ($contacts) => $contacts->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")->orWhere('cpf', 'like', "%{$this->search}%"));
        }
        if ($this->filterGroupId) {
            $query->whereHas('groups', fn ($filter) => $filter->where('contact_groups.id', $this->filterGroupId));
        }

        return view('livewire.admin.contact-manager', ['contacts' => $query->paginate(15), 'groups' => $groups, 'rooms' => Room::whereIn('id', auth('contact')->user()->responsibleRoomIds())->orderBy('name')->get(), 'contactsForGroups' => $this->allowedContacts()->orderBy('name')->get(['contacts.id', 'contacts.name', 'contacts.email']), 'sectors' => $groups->pluck('name')->values()->all(), 'cargos' => ['Defensor', 'Diretor', 'Coordenador', 'Gerente', 'Estagiário'], 'isSuperAdmin' => false]);
    }
}
