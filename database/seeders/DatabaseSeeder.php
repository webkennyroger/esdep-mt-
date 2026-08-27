<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * O sistema nao semeia dados de negocio: salas, grupos, contatos e reservas sao
 * cadastrados pela propria interface. Aqui fica apenas a estrutura de papeis e
 * permissoes, que e infraestrutura de autorizacao e nao conteudo.
 *
 * O primeiro acesso se faz com `php artisan user:super-admin`.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $this->command?->info('Papeis e permissoes garantidos. Crie o primeiro acesso com: php artisan user:super-admin');
    }
}
