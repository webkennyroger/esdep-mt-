<?php

namespace App\Livewire\Admin;

use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RoomManager extends Component
{
    use WithPagination;

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

    // Edit Room Fields
    public int $editRoomId = 0;
    public string $editName = '';
    public string $editCode = '';
    public int $editCapacity = 30;
    public string $editThemeColor = '#008b5c';
    public string $editDescription = '';
    public bool $editIsActive = true;

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
        $this->reset(['name', 'code', 'description']);
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
        $this->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:rooms,code',
            'capacity'    => 'required|integer|min:1|max:1000',
            'theme_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
        ]);

        Room::create([
            'name'        => $this->name,
            'code'        => strtoupper($this->code),
            'capacity'    => $this->capacity,
            'theme_color' => $this->theme_color,
            'description' => $this->description ?: null,
            'is_active'   => $this->is_active,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala criada com sucesso.' } }))");
        $this->dispatch('roomCreated');
        $this->closeCreateModal();
    }

    public function openEditModal(int $id): void
    {
        $room = Room::findOrFail($id);
        $this->editRoomId     = $room->id;
        $this->editName       = $room->name;
        $this->editCode       = $room->code;
        $this->editCapacity   = $room->capacity;
        $this->editThemeColor = $room->theme_color ?? '#008b5c';
        $this->editDescription= $room->description ?? '';
        $this->editIsActive   = (bool) $room->is_active;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function updateRoom(): void
    {
        $this->validate([
            'editName'       => 'required|string|max:255',
            'editCode'       => 'required|string|max:50|unique:rooms,code,' . $this->editRoomId,
            'editCapacity'   => 'required|integer|min:1|max:1000',
            'editThemeColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'editDescription'=> 'nullable|string|max:1000',
            'editIsActive'   => 'boolean',
        ]);

        $room = Room::findOrFail($this->editRoomId);
        $room->update([
            'name'        => $this->editName,
            'code'        => strtoupper($this->editCode),
            'capacity'    => $this->editCapacity,
            'theme_color' => $this->editThemeColor,
            'description' => $this->editDescription ?: null,
            'is_active'   => $this->editIsActive,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala atualizada com sucesso.' } }))");
        $this->dispatch('roomUpdated');
        $this->closeEditModal();
    }

    public function toggleActive(int $id): void
    {
        $room = Room::findOrFail($id);
        $room->is_active = !$room->is_active;
        $room->save();

        $status = $room->is_active ? 'ativada' : 'desativada';
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala {$status} com sucesso.' } }))");
        $this->dispatch('roomUpdated');
    }

    public function confirmDelete(int $id): void
    {
        $room = Room::withCount('reservations')->findOrFail($id);
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
        $room = Room::withCount('reservations')->findOrFail($this->deleteRoomId);

        if ($room->reservations_count > 0) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Não é possível excluir uma sala com histórico de reservas. Você pode desativá-la.' } }))");
            $this->closeDeleteModal();
            return;
        }

        $room->delete();

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Sala excluída com sucesso.' } }))");
        $this->dispatch('roomDeleted');
        $this->closeDeleteModal();
    }

    public function render()
    {
        $query = Room::withCount('reservations')->orderBy('name');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.room-manager', [
            'rooms' => $query->paginate(10),
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
