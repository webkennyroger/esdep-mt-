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
    public bool $showCreateModal = false;
    public bool $showImportModal = false;
    public bool $showGroupModal = false;

    public string $newName = '';
    public string $newEmail = '';
    public array $newGroupIds = [];

    public string $groupName = '';
    public string $groupDescription = '';
    public string $groupColor = '#059669';

    public string $importCsv = '';

    protected $listeners = [
        'contactCreated' => '$refresh',
        'contactDeleted' => '$refresh',
        'groupCreated' => '$refresh',
    ];

    public function openCreateModal(): void
    {
        $this->reset(['newName', 'newEmail', 'newGroupIds']);
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
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        if (!empty($this->newGroupIds)) {
            $contact->groups()->sync($this->newGroupIds);
        }

        session()->flash('success', 'Contato criado com sucesso.');
        $this->dispatch('contactCreated');
        $this->closeCreateModal();
    }

    public function deleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        session()->flash('success', 'Contato removido com sucesso.');
        $this->dispatch('contactDeleted');
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

        session()->flash('success', 'Grupo criado com sucesso.');
        $this->dispatch('groupCreated');
        $this->closeGroupModal();
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

        session()->flash('success', "{$imported} contatos importados. {$skipped} ignorados (ja existentes).");
        $this->dispatch('contactCreated');
        $this->showImportModal = false;
        $this->importCsv = '';
    }

    public function render()
    {
        $query = Contact::with('groups')->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.contact-manager', [
            'contacts' => $query->paginate(15),
            'groups' => ContactGroup::withCount('contacts')->orderBy('name')->get(),
        ]);
    }
}
