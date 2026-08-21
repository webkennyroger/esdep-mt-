<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $groupIds = ContactGroup::pluck('id');

        collect([
            ['group' => 'CDI', 'name' => 'Sala Horizonte', 'code' => 'HORIZONTE', 'capacity' => 60, 'theme_color' => '#008b5c'],
            ['group' => 'CDI', 'name' => 'Sala Ipê', 'code' => 'IPE', 'capacity' => 30, 'theme_color' => '#059669'],
            ['group' => 'INSTITUCIONAL', 'name' => 'Auditório Sede', 'code' => 'SEDE-AUD', 'capacity' => 350, 'theme_color' => '#2563eb'],
            ['group' => 'OUVIDORIA', 'name' => 'Sala 165', 'code' => 'SALA-165', 'capacity' => 10, 'theme_color' => '#7c3aed'],
        ])->each(function (array $data) {
            $groupName = $data['group'];
            unset($data['group']);

            Room::updateOrCreate(['code' => $data['code']], $data + [
                'contact_group_id' => ContactGroup::where('name', $groupName)->value('id'),
                'is_active' => true,
            ]);
        });

        Room::factory()->count(10)->create()->each(function (Room $room) use ($groupIds) {
            $room->update(['contact_group_id' => $groupIds->random()]);
        });
    }
}
