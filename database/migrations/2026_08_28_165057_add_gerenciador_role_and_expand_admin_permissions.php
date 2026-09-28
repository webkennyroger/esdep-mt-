<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Cria o papel "gerenciador" (gerencia so as reservas/relatorios/historico do
 * proprio setor, via Contact vinculado) e promove "admin" a tambem ver
 * contatos, relatorios e historico — continua sem "manage-users".
 */
return new class extends Migration
{
    private const ADMIN_PERMISSIONS = ['access-contacts', 'access-reports', 'access-activity-log'];

    private const GERENCIADOR_PERMISSIONS = ['access-reports', 'access-activity-log'];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(array_map(
            fn (string $name) => Permission::findOrCreate($name, 'web'),
            self::ADMIN_PERMISSIONS
        ));

        $gerenciador = Role::findOrCreate('gerenciador', 'web');
        $gerenciador->syncPermissions(array_map(
            fn (string $name) => Permission::findOrCreate($name, 'web'),
            self::GERENCIADOR_PERMISSIONS
        ));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('admin', 'web')->syncPermissions([]);
        Role::where('name', 'gerenciador')->where('guard_name', 'web')->first()?->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
