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
        $manageUsers = Permission::findOrCreate('manage-users', 'web');

        // "admin": salas, reservas, contatos, relatorios e historico — sem gestao de usuarios.
        Role::findOrCreate('admin', 'web')
            ->syncPermissions([$accessContacts, $accessReports, $accessActivityLog]);

        // "super-admin": tudo que "admin" tem, mais gestao de usuarios.
        Role::findOrCreate('super-admin', 'web')
            ->syncPermissions([$accessContacts, $accessReports, $accessActivityLog, $manageUsers]);

        // "gerenciador": relatorios e historico, mas restritos ao proprio setor
        // (o escopo vem do Contact vinculado, nao da permissao em si).
        Role::findOrCreate('gerenciador', 'web')
            ->syncPermissions([$accessReports, $accessActivityLog]);
    }
}
