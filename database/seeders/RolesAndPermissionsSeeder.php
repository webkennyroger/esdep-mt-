<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $accessContacts = Permission::findOrCreate('access-contacts', 'web');
        $accessReports = Permission::findOrCreate('access-reports', 'web');
        $accessActivityLog = Permission::findOrCreate('access-activity-log', 'web');

        // "admin": acesso operacional (salas, reservas) — sem permissoes extras.
        Role::findOrCreate('admin', 'web');

        // "super-admin": tudo que "admin" tem, mais contatos, relatorios e historico.
        Role::findOrCreate('super-admin', 'web')
            ->syncPermissions([$accessContacts, $accessReports, $accessActivityLog]);
    }
}
