<?php

namespace App\Livewire\Admin;

use App\Models\ContactGroup;
use App\Models\Room;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RoomManager extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    // Modals
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    // Create Room Fields
    public string $name = '';

    public string $code = '';

    public int $capacity = 30;

    public string $theme_color = '#008b5c';

    public string $description = '';

    public bool $is_active = true;

    public ?int $contactGroupId = null;

    // Edit Room Fields
    public int $editRoomId = 0;

    public string $editName = '';

    public string $editCode = '';

    public int $editCapacity = 30;

    public string $editThemeColor = '#008b5c';

    public string $editDescription = '';

    public bool $editIsActive = true;

    public ?int $editContactGroupId = null;

    // Delete Room Fields
    public int $deleteRoomId = 0;

    public string $deleteRoomName = '';

    public int $deleteReservationsCount = 0;

    protected $listeners = [
        'roomCreated' => '$refresh',
        'roomUpdated' => '$refresh',
        'roomDeleted' => '$refresh',
    ];

    public function updatedCode(): void
    {
        $this->code = strtoupper(preg_replace('/[^A-Za-z0-9\-_]/', '', $this->code) ?? '');
    }

    public function updatedEditCode(): void
    {
        $this->editCode = strtoupper(preg_replace('/[^A-Za-z0-9\-_]/', '', $this->editCode) ?? '');
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'code', 'description', 'contactGroupId']);
        $this->capacity = 30;
        $this->theme_color = '#008b5c';
        $this->is_active = true;
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createRoom(): void
    {
        $this->authorize('create', Room::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms,code',
            'capacity' => 'required|integer|min:1|max:1000',
            'theme_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'contactGroupId' => ['nullable', 'integer', Rule::exists('contact_groups', 'id')],
        ]);

        $room = Room::create([
            'contact_group_id' => $this->contactGroupId,
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'capacity' => $this->capacity,
            'theme_color' => $this->theme_color,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ]);

        ActivityLogger::roomCreated($room);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala criada com sucesso.' } }))");
        $this->dispatch('roomCreated');
        $this->closeCreateModal();
    }

    public function openEditModal(int $id): void
    {
        $room = Room::accessibleTo(auth()->user())->findOrFail($id);
        $this->authorize('update', $room);
        $this->editRoomId = $room->id;
        $this->editName = $room->name;
        $this->editCode = $room->code;
        $this->editCapacity = $room->capacity;
        $this->editThemeColor = $room->theme_color ?? '#008b5c';
        $this->editDescription = $room->description ?? '';
        $this->editIsActive = (bool) $room->is_active;
        $this->editContactGroupId = $room->contact_group_id;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function updateRoom(): void
    {
        $room = Room::accessibleTo(auth()->user())->findOrFail($this->editRoomId);
        $this->authorize('update', $room);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editCode' => 'required|string|max:50|unique:rooms,code,'.$this->editRoomId,
            'editCapacity' => 'required|integer|min:1|max:1000',
            'editThemeColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'editDescription' => 'nullable|string|max:1000',
            'editIsActive' => 'boolean',
            'editContactGroupId' => ['nullable', 'integer', Rule::exists('contact_groups', 'id')],
        ]);

        $oldValues = $room->toArray();

        $room->update([
            'contact_group_id' => $this->editContactGroupId,
            'name' => $this->editName,
            'code' => strtoupper($this->editCode),
            'capacity' => $this->editCapacity,
            'theme_color' => $this->editThemeColor,
            'description' => $this->editDescription ?: null,
            'is_active' => $this->editIsActive,
        ]);

        ActivityLogger::roomUpdated($room, $oldValues);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala atualizada com sucesso.' } }))");
        $this->dispatch('roomUpdated');
        $this->closeEditModal();
    }

    public function toggleActive(int $id): void
    {
        $room = Room::accessibleTo(auth()->user())->findOrFail($id);
        $this->authorize('update', $room);
        $room->is_active = ! $room->is_active;
        $room->save();

        $status = $room->is_active ? 'ativada' : 'desativada';
        ActivityLogger::roomUpdated($room, ['is_active' => ! $room->is_active]);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala {$status} com sucesso.' } }))");
        $this->dispatch('roomUpdated');
    }

    public function confirmDelete(int $id): void
    {
        $room = Room::accessibleTo(auth()->user())->withCount('reservations')->findOrFail($id);
        $this->authorize('delete', $room);
        $this->deleteRoomId = $room->id;
        $this->deleteRoomName = $room->name;
        $this->deleteReservationsCount = $room->reservations_count;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteRoomId = 0;
        $this->deleteRoomName = '';
        $this->deleteReservationsCount = 0;
    }

    public function deleteRoom(): void
    {
        $room = Room::accessibleTo(auth()->user())->withCount('reservations')->findOrFail($this->deleteRoomId);
        $this->authorize('delete', $room);

        if ($room->reservations_count > 0) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Não é possível excluir uma sala com histórico de reservas. Você pode desativá-la.' } }))");
            $this->closeDeleteModal();

            return;
        }

        $room->delete();

        ActivityLogger::roomDeleted($room);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala excluída com sucesso.' } }))");
        $this->dispatch('roomDeleted');
        $this->closeDeleteModal();
    }

    public function reorderRooms(array $ids): void
    {
        $query = Room::accessibleTo(auth()->user());
        abort_if((clone $query)->whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            (clone $query)->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    public function render()
    {
        $query = Room::accessibleTo(auth()->user())->with('contactGroup')->withCount('reservations')->orderBy('sort_order')->orderBy('name');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.room-manager', [
            'rooms' => $query->paginate(10),
            'contactGroups' => ContactGroup::whereNull('owner_contact_id')->orderBy('name')->get(),
            'colors' => [
                '#008b5c' => 'Verde ESDEP',
                '#003f32' => 'Verde Escuro',
                '#2563eb' => 'Azul',
                '#7c3aed' => 'Roxo',
                '#d97706' => 'Âmbar',
                '#dc2626' => 'Vermelho',
                '#0891b2' => 'Ciano',
                '#475569' => 'Cinza',
            ],
        ]);
    }
}
