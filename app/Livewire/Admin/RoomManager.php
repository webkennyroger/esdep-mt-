<?php

namespace App\Livewire\Admin;

use App\Models\ContactGroup;
use App\Models\Room;
use App\Rules\MultipleEmails;
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

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    public string $search = '';

    // Modals
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    // Create Room Fields
    public string $name = '';

    public int $capacity = 30;

    public string $theme_color = '#008b5c';

    public string $description = '';

    public bool $is_active = true;

    public ?int $contactGroupId = null;

    public string $notificationEmail = '';

    // Edit Room Fields
    public int $editRoomId = 0;

    public string $editName = '';

    public int $editCapacity = 30;

    public string $editThemeColor = '#008b5c';

    public string $editDescription = '';

    public bool $editIsActive = true;

    public ?int $editContactGroupId = null;

    public string $editNotificationEmail = '';

    // Delete Room Fields
    public int $deleteRoomId = 0;

    public string $deleteRoomName = '';

    public int $deleteReservationsCount = 0;

    protected $listeners = [
        'roomCreated' => '$refresh',
        'roomUpdated' => '$refresh',
        'roomDeleted' => '$refresh',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['name', 'description', 'contactGroupId', 'notificationEmail']);
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
            'capacity' => 'required|integer|min:1|max:1000',
            'theme_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'required|string|max:1000',
            'is_active' => 'boolean',
            'contactGroupId' => ['nullable', 'integer', Rule::exists('contact_groups', 'id')],
            'notificationEmail' => ['nullable', 'string', 'max:255', new MultipleEmails],
        ]);

        $room = Room::create([
            'contact_group_id' => $this->contactGroupId,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'theme_color' => $this->theme_color,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
            'notification_email' => $this->notificationEmail ?: null,
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
        $this->editCapacity = $room->capacity;
        $this->editThemeColor = $room->theme_color ?? '#008b5c';
        $this->editDescription = $room->description ?? '';
        $this->editIsActive = (bool) $room->is_active;
        $this->editContactGroupId = $room->contact_group_id;
        $this->editNotificationEmail = $room->notification_email ?? '';

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
            'editCapacity' => 'required|integer|min:1|max:1000',
            'editThemeColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'editDescription' => 'required|string|max:1000',
            'editIsActive' => 'boolean',
            'editContactGroupId' => ['nullable', 'integer', Rule::exists('contact_groups', 'id')],
            'editNotificationEmail' => ['nullable', 'string', 'max:255', new MultipleEmails],
        ]);

        $oldValues = $room->toArray();

        $room->update([
            'contact_group_id' => $this->editContactGroupId,
            'name' => $this->editName,
            'capacity' => $this->editCapacity,
            'theme_color' => $this->editThemeColor,
            'description' => $this->editDescription ?: null,
            'is_active' => $this->editIsActive,
            'notification_email' => $this->editNotificationEmail ?: null,
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
            $term = '%'.strtolower($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term]);
            });
        }

        return view('livewire.admin.room-manager', [
            'rooms' => $query->paginate(10),
            'contactGroups' => ContactGroup::whereNull('owner_contact_id')->orderBy('name')->get(),
            'colors' => [
                '#008b5c' => 'Verde ESDEP',
                '#003f32' => 'Verde Escuro',
            ] + \App\Support\TailwindColors::palette(),
        ]);
    }
}
