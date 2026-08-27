<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Cria (ou promove) o super-administrador do sistema.
 *
 * Existe para o primeiro acesso em producao: sem um super-admin, ninguem
 * consegue liberar os demais na tela de Usuarios & Acessos. Tambem serve de
 * porta de entrada caso o Keycloak fique indisponivel, ja que o login por senha
 * sobrevive apenas para este papel.
 */
class CreateSuperAdminCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'user:super-admin
        {--email= : E-mail da conta}
        {--name= : Nome exibido}
        {--password= : Senha (evite em producao: fica no historico do shell)}
        {--force : Executa sem pedir confirmacao em producao}';

    protected $description = 'Cria ou promove um super-administrador do sistema';

    /** Senha curta e o unico obstaculo entre um ataque e o acesso total. */
    private const MIN_PASSWORD_LENGTH = 12;

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $email = strtolower(trim((string) ($this->option('email') ?? text(
            label: 'E-mail do super-administrador',
            required: true,
        ))));

        $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // Ao promover alguem que ja existe, o nome cadastrado serve de padrao.
        $name = trim((string) $this->option('name'));

        if ($name === '' && $existing) {
            $name = (string) $existing->name;
        }

        if ($name === '') {
            $name = trim(text(label: 'Nome completo', required: true));
        }

        $plainPassword = $this->resolvePassword($existing);

        $validator = Validator::make(
            ['email' => $email, 'name' => $name, 'password' => $plainPassword],
            [
                'email' => 'required|email|max:255',
                'name' => 'required|string|max:255',
                'password' => ['nullable', 'string', 'min:'.self::MIN_PASSWORD_LENGTH],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = $existing ?? new User;
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        if ($plainPassword !== null) {
            $user->forceFill(['password' => Hash::make($plainPassword)]);
        }

        $user->save();
        $user->syncRoles(['super-admin']);

        $this->newLine();
        $this->info($existing ? "Conta promovida a super-administrador: {$email}" : "Super-administrador criado: {$email}");

        if ($user->keycloak_id === null) {
            $this->line('  Ainda sem vinculo com o SSO. No primeiro login institucional');
            $this->line('  com este mesmo e-mail, a conta sera pareada automaticamente.');
        }

        if ($plainPassword === null && $user->password === null) {
            $this->newLine();
            $this->warn('Esta conta nao tem senha local: so entra pelo SSO.');
            $this->warn('Se o Keycloak ficar indisponivel, nao havera como acessar o sistema.');
        }

        return self::SUCCESS;
    }

    /**
     * Devolve a senha em claro a ser gravada, ou null para manter a atual.
     */
    private function resolvePassword(?User $existing): ?string
    {
        if ($this->option('password')) {
            return (string) $this->option('password');
        }

        // Em deploy automatizado (`-n`), seguir sem senha e valido: a conta
        // entra pelo SSO. Nao ha como perguntar nada nesse contexto.
        if ($this->option('no-interaction') || ! $this->input->isInteractive()) {
            return null;
        }

        $label = $existing
            ? 'Nova senha (deixe vazio para manter a atual)'
            : 'Senha (deixe vazio para permitir apenas login pelo SSO)';

        $plainPassword = password(label: $label);

        if ($plainPassword === '') {
            return null;
        }

        if (password(label: 'Confirme a senha') !== $plainPassword) {
            $this->error('As senhas nao conferem.');

            return $this->resolvePassword($existing);
        }

        return $plainPassword;
    }
}
