<?php

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Room;
use Database\Seeders\DatabaseSeeder;

test('new unified seeder creates and synchronizes groups rooms and responsible contacts', function () {
    $this->seed(DatabaseSeeder::class);

    $group = ContactGroup::where('name', 'ESDEP')->firstOrFail();
    $responsible = Contact::where('email', 'responsavel.esdep@esdep.mt.gov.br')->firstOrFail();

    expect($group->rooms()->count())->toBe(2)
        ->and($responsible->isResponsibleForGroup($group))->toBeTrue()
        ->and(Room::where('code', 'HORIZONTE')->value('contact_group_id'))->toBe($group->id);
});
