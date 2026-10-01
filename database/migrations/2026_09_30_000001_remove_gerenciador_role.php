<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Remove o papel "gerenciador" (setor). Nenhum usuario tinha esse papel
 * atribuido no momento da remocao. As permissions access-reports e
 * access-activity-log continuam existindo (admin/super-admin as usam).
 */
return new class extends Migration
{
    private const GERENCIADOR_PERMISSIONS = ['access-reports', 'access-activity-log'];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', 'gerenciador')->where('guard_name', 'web')->first()?->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $gerenciador = Role::findOrCreate('gerenciador', 'web');
        $gerenciador->syncPermissions(array_map(
            fn (string $name) => Permission::findOrCreate($name, 'web'),
            self::GERENCIADOR_PERMISSIONS
        ));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
