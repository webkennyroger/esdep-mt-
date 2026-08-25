<?php

use App\Livewire\Admin\AdminCalendar;
use App\Livewire\Admin\ContactManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ReservationManager;
use App\Livewire\Contact\ContactCalendar;
use App\Livewire\Contact\ContactDashboard;
use App\Livewire\Contact\ContactGroups;
use App\Livewire\Contact\ContactProfile;
use App\Livewire\Contact\ContactReservationEdit;
use App\Livewire\Contact\ContactReservations;
use App\Livewire\Contact\ContactRooms;
use App\Mail\EventAnnouncementMail;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\EventAnnouncement;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function managedReservation(Room $room, string $name): Reservation
{
    return Reservation::create([
        'room_id' => $room->id, 'event_name' => $name, 'requester_name' => 'Solicitante',
        'requester_email' => 'solicitante@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);
}

test('existing contact group crud synchronizes its managed rooms', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $roomA = Room::factory()->create(['code' => 'GR-A']);
    $roomB = Room::factory()->create(['code' => 'GR-B']);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->set('groupName', 'GRUPO DE SALAS')
        ->set('groupColor', '#008b5c')
        ->set('groupRoomIds', [$roomA->id, $roomB->id])
        ->call('createGroup')
        ->assertHasNoErrors();

    $group = ContactGroup::where('name', 'GRUPO DE SALAS')->firstOrFail();
    expect($roomA->fresh()->contact_group_id)->toBe($group->id)
        ->and($roomB->fresh()->contact_group_id)->toBe($group->id);
});

test('deleting a group unlinks its rooms and contacts without deleting them', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $group = ContactGroup::create(['name' => 'GRUPO PARA EXCLUIR', 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $contact = Contact::create(['name' => 'Contato do grupo', 'email' => 'contato-grupo-excluido@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group);

    Livewire::actingAs($admin)->test(ContactManager::class)->call('deleteGroup', $group->id);

    expect(ContactGroup::find($group->id))->toBeNull()
        ->and(Room::find($room->id))->not->toBeNull()
        ->and($room->fresh()->contact_group_id)->toBeNull()
        ->and(Contact::find($contact->id))->not->toBeNull()
        ->and($contact->groups()->whereKey($group->id)->exists())->toBeFalse();
});

test('admin can select contacts when creating and editing a group', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $alice = Contact::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'password', 'is_active' => true]);
    $bob = Contact::create(['name' => 'Bob', 'email' => 'bob@example.com', 'password' => 'password', 'is_active' => true]);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->set('groupName', 'GRUPO COM CONTATOS')
        ->set('groupColor', '#008b5c')
        ->set('groupContactIds', [$alice->id, $bob->id])
        ->call('createGroup')
        ->assertHasNoErrors();

    $group = ContactGroup::where('name', 'GRUPO COM CONTATOS')->firstOrFail();
    expect($group->contacts()->pluck('contacts.id')->sort()->values()->all())->toBe([$alice->id, $bob->id]);

    $group->contacts()->updateExistingPivot($alice->id, ['is_responsible' => true]);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openEditGroupModal', $group->id)
        ->assertSet('editGroupContactIds', fn ($ids) => in_array($alice->id, $ids) && in_array($bob->id, $ids))
        ->set('editGroupContactIds', [$alice->id])
        ->call('updateGroup')
        ->assertHasNoErrors();

    expect($group->contacts()->pluck('contacts.id')->all())->toBe([$alice->id])
        ->and($group->contacts()->wherePivot('is_responsible', true)->exists())->toBeTrue();
});

test('group user can only attach contacts from their own scope when creating a private group', function () {
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor-privado@example.com', 'password' => 'password', 'is_active' => true]);
    $ownGroup = ContactGroup::create(['owner_contact_id' => $manager->id, 'name' => 'GRUPO PROPRIO', 'color' => '#008b5c']);
    $manager->groups()->attach($ownGroup, ['is_responsible' => true]);
    $ownMember = Contact::create(['name' => 'Membro do grupo', 'email' => 'membro-proprio@example.com', 'password' => 'password', 'is_active' => true]);
    $ownMember->groups()->attach($ownGroup);
    $outsider = Contact::create(['name' => 'Fora do grupo', 'email' => 'fora@example.com', 'password' => 'password', 'is_active' => true]);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->set('groupName', 'GRUPO PRIVADO NOVO')
        ->set('groupColor', '#2563eb')
        ->set('groupContactIds', [$ownMember->id])
        ->call('createGroup')
        ->assertHasNoErrors();

    $privateGroup = ContactGroup::where('name', 'GRUPO PRIVADO NOVO')->firstOrFail();
    expect($privateGroup->contacts()->whereKey($ownMember->id)->exists())->toBeTrue();

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->set('groupName', 'GRUPO INVALIDO')
        ->set('groupColor', '#2563eb')
        ->set('groupContactIds', [$outsider->id])
        ->call('createGroup');

    expect(ContactGroup::where('name', 'GRUPO INVALIDO')->exists())->toBeFalse();
});

test('responsible contact sees and can edit every reservation from group rooms', function () {
    $group = ContactGroup::create(['name' => 'GRUPO A', 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $reservation = managedReservation($room, 'Reserva gerenciada');
    $contact = Contact::create(['name' => 'Responsável', 'email' => 'responsavel@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group, ['is_responsible' => true]);

    $this->actingAs($contact, 'contact')
        ->get(route('contact.dashboard'))
        ->assertOk()
        ->assertSee('Reserva gerenciada');
    $this->get(route('contact.rooms'))->assertOk()->assertSee($room->name);
    $this->get(route('contact.calendar'))->assertOk();
    $this->get(route('contact.reservations'))->assertOk()->assertSee('Reserva gerenciada');
    $this->get(route('contact.profile'))->assertOk();

    Livewire::actingAs($contact, 'contact')->test(ContactDashboard::class)
        ->assertSee('Reserva gerenciada')
        ->assertSee($room->name);

    Livewire::actingAs($contact, 'contact')->test(ContactReservationEdit::class, ['reservation' => $reservation->id])
        ->set('eventName', 'Reserva atualizada pelo responsável')
        ->call('submit')
        ->assertHasNoErrors();

    expect($reservation->fresh()->event_name)->toBe('Reserva atualizada pelo responsável');
});

test('contact cannot manage rooms from a group where they are not responsible', function () {
    $group = ContactGroup::create(['name' => 'GRUPO SEM GESTAO', 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $contact = Contact::create(['name' => 'Membro', 'email' => 'membro@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group, ['is_responsible' => false]);

    Livewire::actingAs($contact, 'contact')->test(ContactRooms::class)->assertDontSee($room->name);
    expect($contact->canManageReservation(managedReservation($room, 'Reserva protegida')))->toBeFalse();
});

test('contact reservations includes reservations owned by the logged contact', function () {
    $room = Room::factory()->create();
    $contact = Contact::create(['name' => 'Solicitante', 'email' => 'proprio@example.com', 'password' => 'password', 'is_active' => true]);
    Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Minha reserva',
        'requester_name' => $contact->name,
        'requester_email' => $contact->email,
        'date' => now()->addDays(2),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
        'status' => 'pending',
    ]);

    Livewire::actingAs($contact, 'contact')
        ->test(ContactReservations::class)
        ->assertSee('Minha reserva');
});

test('responsible contact can approve own group reservation but not another group reservation', function () {
    $groupA = ContactGroup::create(['name' => 'GRUPO A', 'color' => '#008b5c']);
    $groupB = ContactGroup::create(['name' => 'GRUPO B', 'color' => '#2563eb']);
    $ownGroupReservation = managedReservation(Room::factory()->create(['contact_group_id' => $groupA->id]), 'Reserva A');
    $foreignReservation = managedReservation(Room::factory()->create(['contact_group_id' => $groupB->id]), 'Reserva B');
    $contact = Contact::create(['name' => 'Responsável', 'email' => 'responsavel@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($groupA, ['is_responsible' => true]);

    Livewire::actingAs($contact, 'contact')->test(ContactReservations::class)
        ->call('approve', $ownGroupReservation->id);
    expect($ownGroupReservation->fresh()->status->value)->toBe('authorized');

    expect(fn () => Livewire::actingAs($contact, 'contact')->test(ContactReservations::class)
        ->call('approve', $foreignReservation->id))
        ->toThrow(ModelNotFoundException::class);
});

test('contact profile updates login and keeps ownership of existing reservations', function () {
    $room = Room::factory()->create();
    $reservation = managedReservation($room, 'Reserva pessoal');
    $reservation->update(['requester_email' => 'antigo@example.com']);
    $contact = Contact::create(['name' => 'Contato', 'email' => 'antigo@example.com', 'password' => 'password', 'is_active' => true]);

    Livewire::actingAs($contact, 'contact')->test(ContactProfile::class)
        ->set('name', 'Contato Atualizado')
        ->set('email', 'novo@example.com')
        ->set('password', 'senha-nova')
        ->set('passwordConfirmation', 'senha-nova')
        ->call('save')
        ->assertHasNoErrors();

    expect($contact->fresh()->email)->toBe('novo@example.com')
        ->and($reservation->fresh()->requester_email)->toBe('novo@example.com');
});

test('responsible contact cannot access a reservation from another group', function () {
    $groupA = ContactGroup::create(['name' => 'GRUPO A', 'color' => '#008b5c']);
    $groupB = ContactGroup::create(['name' => 'GRUPO B', 'color' => '#2563eb']);
    $roomB = Room::factory()->create(['contact_group_id' => $groupB->id]);
    $foreignReservation = managedReservation($roomB, 'Reserva de outro grupo');
    $contact = Contact::create(['name' => 'Responsável A', 'email' => 'a@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($groupA, ['is_responsible' => true]);

    expect(fn () => Livewire::actingAs($contact, 'contact')
        ->test(ContactReservationEdit::class, ['reservation' => $foreignReservation->id]))
        ->toThrow(ModelNotFoundException::class);
});

test('authorized reservation moved to today appears on both dashboards and calendars', function () {
    $group = ContactGroup::create(['name' => 'GRUPO HOJE', 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $reservation = managedReservation($room, 'Evento autorizado de hoje');
    $reservation->update(['date' => today(), 'status' => 'authorized']);
    $contact = Contact::create(['name' => 'Usuário do grupo', 'email' => 'grupo@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group, ['is_responsible' => false]);
    $admin = User::factory()->create(['role' => 'super_admin']);

    Livewire::actingAs($admin)->test(Dashboard::class)->assertSee('Evento autorizado de hoje');
    Livewire::actingAs($admin)->test(AdminCalendar::class)->call('selectDate', today()->toDateString())->assertSee('Evento autorizado de hoje');
    Livewire::actingAs($contact, 'contact')->test(ContactDashboard::class)->assertSee('Evento autorizado de hoje');
    Livewire::actingAs($contact, 'contact')->test(ContactCalendar::class)->call('selectDate', today()->toDateString())->assertSee('Evento autorizado de hoje');
});

test('group user can announce an authorized reservation only to contacts from own groups', function () {
    Mail::fake();
    $group = ContactGroup::create(['name' => 'GRUPO DIVULGAÇÃO', 'color' => '#008b5c']);
    $otherGroup = ContactGroup::create(['name' => 'OUTRO GRUPO', 'color' => '#2563eb']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $reservation = managedReservation($room, 'Evento para divulgar');
    $reservation->update(['status' => 'authorized']);
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor@example.com', 'password' => 'password', 'is_active' => true]);
    $recipient = Contact::create(['name' => 'Destinatário', 'email' => 'destinatario@example.com', 'password' => 'password', 'is_active' => true]);
    $outsider = Contact::create(['name' => 'Externo', 'email' => 'externo@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($group, ['is_responsible' => true]);
    $recipient->groups()->attach($group);
    $outsider->groups()->attach($otherGroup);

    Livewire::actingAs($manager, 'contact')->test(ContactReservations::class)
        ->assertSee('Divulgar')
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->set('announceRecipientType', 'groups')
        ->set('announceSelectedGroupIds', [$group->id])
        ->call('sendAnnouncement')
        ->assertHasNoErrors();

    $announcement = EventAnnouncement::firstOrFail();
    expect($announcement->created_by)->toBeNull()
        ->and($announcement->created_by_contact_id)->toBe($manager->id)
        ->and($announcement->total_recipients)->toBe(2);
    Mail::assertOutgoingCount(3);
    Mail::assertQueued(ReservationApprovedMail::class, fn (ReservationApprovedMail $mail) => $mail->hasTo($reservation->requester_email));
    Mail::assertNotSent(fn (EventAnnouncementMail $mail) => $mail->hasTo($outsider->email));
});

test('announcing without selecting extra recipients only notifies the requester (and room owner via cc)', function () {
    Mail::fake();
    $group = ContactGroup::create(['name' => 'GRUPO SEM SELECAO', 'color' => '#008b5c', 'notification_email' => 'dono@example.com']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);
    $reservation = managedReservation($room, 'Evento sem destinatarios extras');
    $reservation->update(['status' => 'authorized']);
    $manager = Contact::create(['name' => 'Gestor Sem Selecao', 'email' => 'gestor-sem-selecao@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($group, ['is_responsible' => true]);

    Livewire::actingAs($manager, 'contact')->test(ContactReservations::class)
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->call('sendAnnouncement')
        ->assertHasNoErrors();

    $announcement = EventAnnouncement::firstOrFail();
    expect($announcement->total_recipients)->toBe(0);
    Mail::assertOutgoingCount(1);
    Mail::assertQueued(ReservationApprovedMail::class, fn (ReservationApprovedMail $mail) => $mail->hasTo($reservation->requester_email) && $mail->hasCc('dono@example.com'));
});

test('status changes do not send email before the reservation is disclosed', function () {
    Mail::fake();
    $room = Room::factory()->create();
    $reservation = managedReservation($room, 'Reserva sem notificação automática');
    $admin = User::factory()->create(['role' => 'super_admin']);

    Livewire::actingAs($admin)->test(ReservationManager::class)
        ->call('openApproveModal', $reservation->id, $reservation->event_name)
        ->call('confirmApprove');

    Mail::assertNothingSent();

    $reservation->refresh();
    $reservation->update(['status' => 'rejected']);
    Livewire::actingAs($admin)->test(ReservationManager::class)
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->call('sendAnnouncement');

    Mail::assertQueued(ReservationRejectedMail::class, fn (ReservationRejectedMail $mail) => $mail->hasTo($reservation->requester_email));
});

test('group user can manage own rooms but cannot act on a room from another group', function () {
    $group = ContactGroup::create(['name' => 'GRUPO SALAS', 'color' => '#008b5c']);
    $otherGroup = ContactGroup::create(['name' => 'GRUPO EXTERNO', 'color' => '#2563eb']);
    $room = Room::factory()->create(['contact_group_id' => $group->id, 'is_active' => true]);
    $foreignRoom = Room::factory()->create(['contact_group_id' => $otherGroup->id]);
    $contact = Contact::create(['name' => 'Gestor da sala', 'email' => 'salas@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group, ['is_responsible' => true]);

    Livewire::actingAs($contact, 'contact')->test(ContactRooms::class)
        ->assertSee('Nova Sala')
        ->assertSee('Editar Sala')
        ->call('toggleActive', $room->id)
        ->assertHasNoErrors();

    expect($room->fresh()->is_active)->toBeFalse()
        ->and(fn () => Livewire::actingAs($contact, 'contact')->test(ContactRooms::class)->call('openEditModal', $foreignRoom->id))
        ->toThrow(ModelNotFoundException::class);
});

test('group user has full contact tools scoped to own groups', function () {
    $group = ContactGroup::create(['name' => 'GRUPO CONTATOS', 'color' => '#008b5c']);
    $foreignGroup = ContactGroup::create(['name' => 'GRUPO CONTATOS EXTERNO', 'color' => '#2563eb']);
    $manager = Contact::create(['name' => 'Gestor de contatos', 'email' => 'gestor-contatos@example.com', 'password' => 'password', 'is_active' => true]);
    $local = Contact::create(['name' => 'Contato local', 'email' => 'local@example.com', 'password' => 'password', 'is_active' => true]);
    $foreign = Contact::create(['name' => 'Contato externo', 'email' => 'externo-grupo@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($group, ['is_responsible' => true]);
    $local->groups()->attach($group);
    $foreign->groups()->attach($foreignGroup);
    $group->update(['owner_contact_id' => $manager->id]);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->assertDontSee('Importar')
        ->assertDontSee('Novo contato')
        ->assertSee('Contato local')
        ->assertDontSee('Contato externo')
        ->set('newName', 'Novo contato do grupo')
        ->set('newEmail', 'novo-grupo@example.com')
        ->set('newPassword', 'senha-segura')
        ->set('newGroupIds', [$group->id])
        ->call('createContact')
        ->assertHasNoErrors()
        ->set('filterGroupId', $group->id)
        ->set('importCsv', 'Contato importado,importado@example.com')
        ->call('importContacts')
        ->assertHasNoErrors();

    expect(Contact::where('email', 'novo-grupo@example.com')->firstOrFail()->groups()->whereKey($group->id)->exists())->toBeTrue()
        ->and(Contact::where('email', 'importado@example.com')->firstOrFail()->groups()->whereKey($group->id)->exists())->toBeTrue()
        ->and(fn () => Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)->call('openEditModal', $foreign->id))
        ->toThrow(ModelNotFoundException::class);
});

test('group user cannot delete a contact that owns another private group', function () {
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor-dono@example.com', 'password' => 'password', 'is_active' => true]);
    $ownedGroup = ContactGroup::create(['name' => 'GRUPO PRIMARIO', 'color' => '#008b5c']);
    $manager->groups()->attach($ownedGroup, ['is_responsible' => true]);

    $secondaryOwner = Contact::create(['name' => 'Contato dono de outro grupo', 'email' => 'dono-secundario@example.com', 'password' => 'password', 'is_active' => true]);
    $secondaryGroup = ContactGroup::create(['owner_contact_id' => $secondaryOwner->id, 'name' => 'GRUPO SECUNDARIO', 'color' => '#2563eb']);
    $secondaryOwner->groups()->attach($ownedGroup);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->call('deleteContact', $secondaryOwner->id);

    expect(Contact::find($secondaryOwner->id))->not->toBeNull()
        ->and(ContactGroup::find($secondaryGroup->id))->not->toBeNull();
});

test('group user cannot delete a contact with reservation history', function () {
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor-historico@example.com', 'password' => 'password', 'is_active' => true]);
    $group = ContactGroup::create(['name' => 'GRUPO HISTORICO', 'color' => '#008b5c']);
    $manager->groups()->attach($group, ['is_responsible' => true]);

    $requester = Contact::create(['name' => 'Solicitante com historico', 'email' => 'com-historico@example.com', 'password' => 'password', 'is_active' => true]);
    $requester->groups()->attach($group);
    $room = Room::factory()->create();
    Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento antigo', 'requester_name' => 'Solicitante com historico',
        'requester_email' => 'com-historico@example.com', 'date' => now()->subDays(10),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'authorized',
    ]);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->call('deleteContact', $requester->id);

    expect(Contact::find($requester->id))->not->toBeNull();
});

test('group user can move a contact between groups owned by their account', function () {
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor-troca@example.com', 'password' => 'password', 'is_active' => true]);
    $groupA = ContactGroup::create(['owner_contact_id' => $manager->id, 'name' => 'GRUPO TROCA A', 'color' => '#008b5c']);
    $groupB = ContactGroup::create(['owner_contact_id' => $manager->id, 'name' => 'GRUPO TROCA B', 'color' => '#2563eb']);
    $target = Contact::create(['name' => 'Contato para mover', 'email' => 'mover@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach([$groupA->id, $groupB->id], ['is_responsible' => true]);
    $target->groups()->attach($groupA, ['is_responsible' => false]);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->call('openEditModal', $target->id)
        ->assertSet('editGroupIds', [$groupA->id])
        ->set('editGroupIds', [$groupB->id])
        ->set('editResponsibleGroupIds', [$groupB->id])
        ->call('updateContact')
        ->assertHasNoErrors();

    expect($target->groups()->whereKey($groupA->id)->exists())->toBeFalse()
        ->and($target->groups()->whereKey($groupB->id)->exists())->toBeTrue()
        ->and((bool) $target->groups()->whereKey($groupB->id)->firstOrFail()->pivot->is_responsible)->toBeTrue();
});

test('group user cannot remove a contact from the access group defined by super admin', function () {
    $accessGroup = ContactGroup::create(['name' => 'GRUPO DE ACESSO', 'color' => '#008b5c']);
    $manager = Contact::create(['name' => 'Gestor', 'email' => 'gestor-acesso@example.com', 'password' => 'password', 'is_active' => true]);
    $target = Contact::create(['name' => 'Contato protegido', 'email' => 'protegido@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($accessGroup);
    $target->groups()->attach($accessGroup);

    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->call('openEditModal', $target->id)
        ->assertSet('editGroupIds', [])
        ->set('editGroupIds', [])
        ->call('updateContact')
        ->assertHasNoErrors()
        ->assertSee('Contato protegido');

    expect($target->groups()->whereKey($accessGroup->id)->exists())->toBeTrue();
});

test('super admin can move a contact between global groups without changing private groups', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $owner = Contact::create(['name' => 'Proprietário', 'email' => 'proprietario@example.com', 'password' => 'password', 'is_active' => true]);
    $target = Contact::create(['name' => 'Contato global', 'email' => 'global@example.com', 'password' => 'password', 'is_active' => true]);
    $groupA = ContactGroup::create(['name' => 'GLOBAL A', 'color' => '#008b5c']);
    $groupB = ContactGroup::create(['name' => 'GLOBAL B', 'color' => '#2563eb']);
    $privateGroup = ContactGroup::create(['owner_contact_id' => $owner->id, 'name' => 'PRIVADO PRESERVADO', 'color' => '#7c3aed']);
    $target->groups()->attach([$groupA->id, $privateGroup->id]);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openEditModal', $target->id)
        ->assertSet('editGroupIds', [$groupA->id])
        ->set('editGroupIds', [$groupB->id])
        ->call('updateContact')
        ->assertHasNoErrors()
        ->assertSee('GLOBAL B');

    expect($target->groups()->whereKey($groupA->id)->exists())->toBeFalse()
        ->and($target->groups()->whereKey($groupB->id)->exists())->toBeTrue()
        ->and($target->groups()->whereKey($privateGroup->id)->exists())->toBeTrue();

    Livewire::actingAs($target, 'contact')->test(ContactGroups::class)
        ->assertSee('GLOBAL B')
        ->assertDontSee('GLOBAL A');
});

test('contact table refreshes when super admin moves another contact into its group', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $groupA = ContactGroup::create(['name' => 'EQUIPE A', 'color' => '#008b5c']);
    $groupB = ContactGroup::create(['name' => 'EQUIPE B', 'color' => '#2563eb']);
    $manager = Contact::create(['name' => 'Gestor da equipe B', 'email' => 'gestor-b@example.com', 'password' => 'password', 'is_active' => true]);
    $target = Contact::create(['name' => 'Contato transferido', 'email' => 'transferido@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($groupB);
    $target->groups()->attach($groupA);

    $userTable = Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)
        ->assertDontSee('Contato transferido');

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openEditModal', $target->id)
        ->set('editGroupIds', [$groupB->id])
        ->call('updateContact')
        ->assertHasNoErrors();

    $this->actingAs($manager, 'contact');
    $userTable->call('$refresh')->assertSee('Contato transferido');
});

test('legacy contact without membership is linked to matching global group', function () {
    $group = ContactGroup::create(['name' => 'CDI', 'color' => '#008b5c']);
    $contact = Contact::create(['name' => 'Contato legado', 'email' => 'legado@example.com', 'setor' => 'CDI', 'password' => 'password', 'is_active' => true]);

    $migration = require database_path('migrations/2026_08_20_140000_backfill_missing_contact_group_memberships.php');
    $migration->up();

    expect($contact->groups()->whereKey($group->id)->exists())->toBeTrue();
});

test('group color selection is visibly preserved for admin and group user', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openGroupModal')
        ->set('groupColor', '#2563eb')
        ->assertSet('groupColor', '#2563eb')
        ->assertSeeHtml('aria-pressed="true"');

    $group = ContactGroup::create(['name' => 'GRUPO COR', 'color' => '#008b5c']);
    $contact = Contact::create(['name' => 'Gestor cor', 'email' => 'cor@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group);
    Livewire::actingAs($contact, 'contact')->test(ContactGroups::class)
        ->call('openGroupModal')
        ->set('groupColor', '#7c3aed')
        ->assertSet('groupColor', '#7c3aed')
        ->assertSeeHtml('aria-pressed="true"');
});

test('group created by contact is private and hidden from super admin', function () {
    $baseGroup = ContactGroup::create(['name' => 'GRUPO BASE PRIVADO', 'color' => '#008b5c']);
    $contact = Contact::create(['name' => 'Criador privado', 'email' => 'privado@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($baseGroup);

    Livewire::actingAs($contact, 'contact')->test(ContactGroups::class)
        ->set('groupName', 'GRUPO PARTICULAR')
        ->set('groupColor', '#7c3aed')
        ->call('createGroup')
        ->assertHasNoErrors()
        ->assertSee('GRUPO PARTICULAR')
        ->assertSee('GRUPO BASE PRIVADO');

    $privateGroup = ContactGroup::where('name', 'GRUPO PARTICULAR')->firstOrFail();
    expect($privateGroup->owner_contact_id)->toBe($contact->id);

    $admin = User::factory()->create(['role' => 'super_admin']);
    Livewire::actingAs($admin)->test(ContactManager::class)->assertDontSee('GRUPO PARTICULAR');
    expect(fn () => Livewire::actingAs($admin)->test(ContactManager::class)->call('confirmDeleteGroup', $privateGroup->id))
        ->toThrow(ModelNotFoundException::class);
});

test('group user can persist drag and drop order only inside own scope', function () {
    $group = ContactGroup::create(['name' => 'GRUPO ORDENÁVEL', 'color' => '#008b5c']);
    $roomA = Room::factory()->create(['contact_group_id' => $group->id]);
    $roomB = Room::factory()->create(['contact_group_id' => $group->id]);
    $reservationA = managedReservation($roomA, 'Evento A');
    $reservationB = managedReservation($roomB, 'Evento B');
    $manager = Contact::create(['name' => 'Gestor ordem', 'email' => 'ordem@example.com', 'password' => 'password', 'is_active' => true]);
    $contactA = Contact::create(['name' => 'Contato A', 'email' => 'ordem-a@example.com', 'password' => 'password', 'is_active' => true]);
    $contactB = Contact::create(['name' => 'Contato B', 'email' => 'ordem-b@example.com', 'password' => 'password', 'is_active' => true]);
    $manager->groups()->attach($group, ['is_responsible' => true]);
    $contactA->groups()->attach($group);
    $contactB->groups()->attach($group);
    $group->update(['owner_contact_id' => $manager->id]);
    $secondGroup = ContactGroup::create(['owner_contact_id' => $manager->id, 'name' => 'SEGUNDO GRUPO ORDENÁVEL', 'color' => '#7c3aed']);
    $manager->groups()->attach($secondGroup);

    Livewire::actingAs($manager, 'contact')->test(ContactRooms::class)->assertSeeHtml('draggable="false"')->assertSeeHtml('data-drag-handle')->call('reorderRooms', [$roomB->id, $roomA->id]);
    Livewire::actingAs($manager, 'contact')->test(ContactReservations::class)->assertSeeHtml('draggable="false"')->assertSeeHtml('data-drag-handle')->call('reorderReservations', [$reservationB->id, $reservationA->id]);
    Livewire::actingAs($manager, 'contact')->test(ContactGroups::class)->assertSeeHtml('draggable="false"')->assertSeeHtml('data-drag-handle')->call('reorderGroups', [$secondGroup->id, $group->id])->call('reorderContacts', [$contactB->id, $contactA->id, $manager->id]);

    expect($secondGroup->fresh()->sort_order)->toBe(0)->and($group->fresh()->sort_order)->toBe(1)
        ->and($roomB->fresh()->sort_order)->toBe(0)->and($roomA->fresh()->sort_order)->toBe(1)
        ->and($reservationB->fresh()->sort_order)->toBe(0)->and($reservationA->fresh()->sort_order)->toBe(1)
        ->and($contactB->fresh()->sort_order)->toBe(0)->and($contactA->fresh()->sort_order)->toBe(1);
});
