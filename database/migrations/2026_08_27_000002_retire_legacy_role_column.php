<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Aposenta a coluna legada `users.role`, que era a fonte de verdade real dos
 * papeis: um hook no model a ressincronizava a cada `save()`, desfazendo
 * qualquer `assignRole()` no login seguinte. A partir daqui o Spatie e a unica
 * fonte de verdade.
 *
 * Os papeis canonicos sao criados aqui (e nao apenas no seeder) porque o hook
 * antigo os criava sob demanda; sem isso, um banco recem-migrado ficaria sem
 * papel nenhum.
 */
return new class extends Migration
{
    /**
     * Permissoes exclusivas do super-admin. O papel `admin` cobre salas e
     * reservas, que sao autorizadas por policy e nao por permissao nomeada.
     */
    private const SUPER_ADMIN_PERMISSIONS = [
        'access-contacts',
        'access-reports',
        'access-activity-log',
        'manage-users',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin', 'web');
        $superAdmin = Role::findOrCreate('super-admin', 'web');

        $superAdmin->syncPermissions(array_map(
            fn (string $name) => Permission::findOrCreate($name, 'web'),
            self::SUPER_ADMIN_PERMISSIONS
        ));

        if (Schema::hasColumn('users', 'role')) {
            // Converte o valor legado em atribuicao real antes de perder a coluna.
            // 'manager' nunca chegou a ser usado (o "Gerente" do sistema e um
            // cargo de Contact, nao um papel de User), mas e mapeado por seguranca.
            foreach (DB::table('users')->select('id', 'role')->get() as $row) {
                $roleId = match ($row->role) {
                    'super_admin', 'super-admin' => $superAdmin->id,
                    'admin', 'manager' => $admin->id,
                    default => null,
                };

                if ($roleId === null) {
                    continue;
                }

                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $roleId,
                    'model_type' => User::class,
                    'model_id' => $row->id,
                ]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
        });

        // Repovoa a coluna a partir dos papeis, para que um rollback nao perca
        // a informacao de quem era admin.
        $roles = DB::table('roles')->pluck('name', 'id');

        foreach (DB::table('model_has_roles')->where('model_type', User::class)->get() as $row) {
            $name = $roles[$row->role_id] ?? null;

            if ($name === null) {
                continue;
            }

            DB::table('users')->where('id', $row->model_id)->update(['role' => $name]);
        }
    }
};
