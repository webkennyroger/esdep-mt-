<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name'        => 'Sala Horizonte',
                'code'        => 'HORIZONTE',
                'capacity'    => 60,
                'description' => 'Sala de reunioes no 1o andar',
                'theme_color' => '#008b5c',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sala Ipe',
                'code'        => 'IPE',
                'capacity'    => 30,
                'description' => 'Sala principal para eventos',
                'theme_color' => '#0066aa',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sala 165',
                'code'        => 'SALA-165',
                'capacity'    => 10,
                'description' => 'Sala de reuniao pequena',
                'theme_color' => '#7c3aed',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sede Auditório ',
                'code'        => 'SEDE-AUD',
                'capacity'    => 350,
                'description' => 'Sala Auditório Sede',
                'theme_color' => '#0f2d4f',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sede reuniões  ',
                'code'        => 'REUN-SEDE',
                'capacity'    => 350,
                'description' => 'Sala de reuniões Sede',
                'theme_color' => '#0f2d4f',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sala Conselho  ',
                'code'        => 'CONSELHO',
                'capacity'    => 15,
                'description' => 'Sala de conselho',
                'theme_color' => '#c27ba0',
                'is_active'   => true,
            ],

        ];

        foreach ($rooms as $room) {
            \App\Models\Room::updateOrCreate(['code' => $room['code']], $room);
        }
    }
}