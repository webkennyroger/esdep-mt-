<?php

use App\Actions\Auth\ResolveKeycloakUserAction;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use SocialiteProviders\Manager\OAuth2\User as SocialiteUser;

/**
 * Cobre a autorizacao por setor no `User` — antes ela existia apenas no
 * `Contact`, ligada ao guard `contact`. Este e o passo que permite trocar o
 * guard depois sem reescrever as regras de acesso.
 */
function sectorSetup(bool $responsible, string $email = 'servidor@defensoria.mt.def.br'): array
{
    $group = ContactGroup::create(['name' => 'SETOR '.strtoupper(substr(md5($email), 0, 5)), 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);

    $user = User::factory()->create(['email' => $email]);
    $contact = Contact::create([
        'name' => 'Servidor', 'email' => $email, 'password' => 'password',
        'is_active' => true, 'user_id' => $user->id,
    ]);
    $contact->groups()->attach($group, ['is_responsible' => $responsible]);

    return [$user->fresh(), $room, $group];
}

function reservationIn(Room $room, string $requester = 'outra.pessoa@example.com'): Reservation
{
    return Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento', 'requester_name' => 'Alguem',
        'requester_email' => $requester, 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);
}

test('um usuario sem contato vinculado nao alcanca sala nem reserva alguma', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();
    reservationIn($room);

    expect(Room::accessibleTo($user)->count())->toBe(0)
        ->and(Reservation::accessibleTo($user)->count())->toBe(0);
});

test('quem pertence a um setor enxerga as salas daquele setor', function () {
    [$user, $room] = sectorSetup(responsible: false);
    Room::factory()->create(); // sala de outro setor, fora do alcance

    $accessible = Room::accessibleTo($user)->pluck('rooms.id');

    expect($accessible->all())->toBe([$room->id]);
});

test('quem pertence a um setor enxerga as reservas das salas do setor', function () {
    [$user, $room] = sectorSetup(responsible: false);
    $doSetor = reservationIn($room);
    reservationIn(Room::factory()->create()); // reserva de outro setor

    expect(Reservation::accessibleTo($user)->pluck('id')->all())->toBe([$doSetor->id]);
});

test('a pessoa sempre enxerga o que ela mesma solicitou, mesmo fora do seu setor', function () {
    [$user] = sectorSetup(responsible: false);
    $minha = reservationIn(Room::factory()->create(), $user->email);

    expect(Reservation::accessibleTo($user)->pluck('id')->all())->toBe([$minha->id]);
});

test('so o responsavel pelo setor pode gerenciar as reservas daquelas salas', function () {
    [$responsavel, $salaDoResponsavel] = sectorSetup(responsible: true, email: 'responsavel@defensoria.mt.def.br');
    [$membro] = sectorSetup(responsible: false, email: 'membro@defensoria.mt.def.br');

    $reserva = reservationIn($salaDoResponsavel);

    expect($responsavel->canManageReservation($reserva))->toBeTrue()
        ->and($membro->canManageReservation($reserva))->toBeFalse();
});

test('um admin gerencia qualquer reserva, independente de setor', function () {
    $admin = User::factory()->admin()->create();
    $reserva = reservationIn(Room::factory()->create());

    expect($admin->canManageReservation($reserva))->toBeTrue()
        ->and(Reservation::accessibleTo($admin)->count())->toBe(1);
});

test('o login pelo sso vincula o cadastro de contato de mesmo e-mail', function () {
    $contact = Contact::create([
        'name' => 'Servidor Existente', 'email' => 'ja.cadastrado@defensoria.mt.def.br',
        'password' => 'password', 'is_active' => true,
    ]);

    $ssoUser = new SocialiteUser;
    $ssoUser->map(['id' => 'kc-vinculo-1', 'name' => 'Servidor Existente', 'email' => 'ja.cadastrado@defensoria.mt.def.br']);
    $ssoUser->setRaw([]);

    $user = app(ResolveKeycloakUserAction::class)->execute($ssoUser);

    expect($contact->fresh()->user_id)->toBe($user->id)
        ->and($user->contact->id)->toBe($contact->id);
});

test('o sso nao rouba um contato que ja pertence a outra conta', function () {
    $dono = User::factory()->create();
    $contact = Contact::create([
        'name' => 'Ja Vinculado', 'email' => 'ja.vinculado@defensoria.mt.def.br',
        'password' => 'password', 'is_active' => true, 'user_id' => $dono->id,
    ]);

    $ssoUser = new SocialiteUser;
    $ssoUser->map(['id' => 'kc-intruso-1', 'name' => 'Outro', 'email' => 'ja.vinculado@defensoria.mt.def.br']);
    $ssoUser->setRaw([]);

    app(ResolveKeycloakUserAction::class)->execute($ssoUser);

    expect($contact->fresh()->user_id)->toBe($dono->id);
});
