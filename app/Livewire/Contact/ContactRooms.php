<?php

namespace App\Livewire\Contact;

use App\Models\ContactGroup;
use App\Models\Room;
use App\Services\ActivityLogger;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.contact', ['title' => 'Minhas Salas — ESDEP-MT'])]
class ContactRooms extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public string $name = '';

    public int $capacity = 30;

    public string $theme_color = '#008b5c';

    public string $description = '';

    public bool $is_active = true;

    public ?int $contactGroupId = null;

    public int $editRoomId = 0;

    public string $editName = '';

    public int $editCapacity = 30;

    public string $editThemeColor = '#008b5c';

    public string $editDescription = '';

    public bool $editIsActive = true;

    public ?int $editContactGroupId = null;

    public int $deleteRoomId = 0;

    public string $deleteRoomName = '';

    public int $deleteReservationsCount = 0;

    public function openCreateModal(): void
    {
        $this->reset(['name', 'description']);
        $this->capacity = 30;
        $this->theme_color = '#008b5c';
        $this->is_active = true;
        $this->contactGroupId = $this->responsibleGroupIds()->first();
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
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000', 'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => 'nullable|string|max:1000', 'is_active' => 'boolean',
            'contactGroupId' => ['required', 'integer', Rule::in($this->responsibleGroupIds()->all())],
        ]);
        $room = Room::create(['contact_group_id' => $this->contactGroupId, 'name' => $this->name, 'capacity' => $this->capacity, 'theme_color' => $this->theme_color, 'description' => $this->description ?: null, 'is_active' => $this->is_active]);
        ActivityLogger::roomCreated($room);
        $this->closeCreateModal();
        $this->toast('Sala criada com sucesso.');
    }

    public function openEditModal(int $id): void
    {
        $room = $this->managedRoom($id);
        $this->editRoomId = $room->id;
        $this->editName = $room->name;
        $this->editCapacity = $room->capacity;
        $this->editThemeColor = $room->theme_color ?? '#008b5c';
        $this->editDescription = $room->description ?? '';
        $this->editIsActive = $room->is_active;
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
        $room = $this->managedRoom($this->editRoomId);
        $this->validate([
            'editName' => 'required|string|max:255',
            'editCapacity' => 'required|integer|min:1|max:1000', 'editThemeColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'editDescription' => 'nullable|string|max:1000', 'editIsActive' => 'boolean',
            'editContactGroupId' => ['required', 'integer', Rule::in($this->responsibleGroupIds()->all())],
        ]);
        $old = $room->toArray();
        $room->update(['contact_group_id' => $this->editContactGroupId, 'name' => $this->editName, 'capacity' => $this->editCapacity, 'theme_color' => $this->editThemeColor, 'description' => $this->editDescription ?: null, 'is_active' => $this->editIsActive]);
        ActivityLogger::roomUpdated($room, $old);
        $this->closeEditModal();
        $this->toast('Sala atualizada com sucesso.');
    }

    public function toggleActive(int $id): void
    {
        $room = $this->managedRoom($id);
        $old = $room->is_active;
        $room->update(['is_active' => ! $old]);
        ActivityLogger::roomUpdated($room, ['is_active' => $old]);
        $this->toast('Status da sala atualizado.');
    }

    public function confirmDelete(int $id): void
    {
        $room = $this->managedRoom($id)->loadCount('reservations');
        $this->deleteRoomId = $room->id;
        $this->deleteRoomName = $room->name;
        $this->deleteReservationsCount = (int) $room->getAttribute('reservations_count');
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
        $room = $this->managedRoom($this->deleteRoomId)->loadCount('reservations');
        if ((int) $room->getAttribute('reservations_count') > 0) {
            $this->closeDeleteModal();
            $this->toast('Não é possível excluir uma sala com reservas. Desative-a.', 'error');

            return;
        }
        ActivityLogger::roomDeleted($room);
        $room->delete();
        $this->closeDeleteModal();
        $this->toast('Sala excluída com sucesso.');
    }

    public function reorderRooms(array $ids): void
    {
        abort_if(Room::whereIn('id', auth('contact')->user()->responsibleRoomIds())->whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            Room::whereIn('id', auth('contact')->user()->responsibleRoomIds())->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    private function responsibleGroupIds()
    {
        return auth('contact')->user()->responsibleGroups()->pluck('contact_groups.id');
    }

    private function managedRoom(int $id): Room
    {
        return Room::whereKey($id)->whereIn('id', auth('contact')->user()->responsibleRoomIds())->firstOrFail();
    }

    private function toast(string $message, string $type = 'success'): void
    {
        $detail = json_encode(compact('type', 'message'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: {$detail} }))");
    }

    public function render()
    {
        $contact = auth('contact')->user();
        $query = Room::whereIn('id', $contact->responsibleRoomIds())->with('contactGroup')->withCount('reservations')->orderBy('sort_order')->orderBy('name');
        if ($this->search !== '') {
            $query->where(fn ($rooms) => $rooms->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%"));
        }

        return view('livewire.admin.room-manager', [
            'rooms' => $query->paginate(10),
            'contactGroups' => ContactGroup::whereIn('id', $this->responsibleGroupIds())->orderBy('name')->get(),
            'colors' => ['#008b5c' => 'Verde ESDEP', '#003f32' => 'Verde Escuro'] + \App\Support\TailwindColors::palette(),
        ]);
    }
}
