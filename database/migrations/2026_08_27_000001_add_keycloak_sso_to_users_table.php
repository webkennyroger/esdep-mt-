<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // `sub` do Keycloak: identificador imutavel do usuario no realm.
            // E a chave de vinculo com o SSO — o e-mail muda, o `sub` nao.
            $table->string('keycloak_id')->nullable()->unique()->after('email');

            // Quem entra pela primeira vez via SSO nasce inativo e aguarda
            // liberacao de um administrador (decisao de projeto: fail closed).
            $table->boolean('is_active')->default(false)->after('role');
            $table->timestamp('activated_at')->nullable()->after('is_active');
            $table->foreignId('activated_by_id')->nullable()->after('activated_at')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('last_login_at')->nullable()->after('activated_by_id');
        });

        // Usuarios que ja existiam foram criados deliberadamente por um admin,
        // entao continuam ativos — o default `false` vale so para os novos.
        DB::table('users')->update(['is_active' => true, 'activated_at' => now()]);

        // Contas de SSO nao possuem senha local.
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('activated_by_id');
            $table->dropColumn(['keycloak_id', 'is_active', 'activated_at', 'last_login_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
