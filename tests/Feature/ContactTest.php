<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use App\Models\ContactGroup;
use Livewire\Livewire;
use App\Livewire\Admin\ContactManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_contact(): void
    {
        Livewire::test(ContactManager::class)
            ->set('newName', 'Carlos Souza')
            ->set('newEmail', 'carlos@test.com')
            ->call('createContact')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'name' => 'Carlos Souza',
            'email' => 'carlos@test.com',
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_duplicate_email(): void
    {
        Contact::create([
            'name' => 'Carlos',
            'email' => 'carlos@test.com',
            'is_active' => true,
        ]);

        Livewire::test(ContactManager::class)
            ->set('newName', 'Outro Carlos')
            ->set('newEmail', 'carlos@test.com')
            ->call('createContact')
            ->assertHasErrors(['newEmail']);
    }

    public function test_toggle_contact_active(): void
    {
        $contact = Contact::create([
            'name' => 'Carlos',
            'email' => 'carlos@test.com',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        Livewire::test(ContactManager::class)
            ->call('toggleActive', $contact->id);

        $contact->refresh();
        $this->assertFalse($contact->is_active);
        $this->assertNotNull($contact->unsubscribed_at);
    }

    public function test_create_group(): void
    {
        Livewire::test(ContactManager::class)
            ->set('groupName', 'Defensores')
            ->set('groupDescription', 'Grupo de defensores')
            ->call('createGroup')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_groups', ['name' => 'Defensores']);
    }

    public function test_import_contacts(): void
    {
        $csv = "nome,email\nCarlos Souza,carlos@test.com\nMaria Silva,maria@test.com";

        Livewire::test(ContactManager::class)
            ->set('importCsv', $csv)
            ->call('importContacts')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', ['email' => 'carlos@test.com']);
        $this->assertDatabaseHas('contacts', ['email' => 'maria@test.com']);
    }

    public function test_delete_contact(): void
    {
        $contact = Contact::create([
            'name' => 'Carlos',
            'email' => 'carlos@test.com',
            'is_active' => true,
        ]);

        Livewire::test(ContactManager::class)
            ->call('deleteContact', $contact->id);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
