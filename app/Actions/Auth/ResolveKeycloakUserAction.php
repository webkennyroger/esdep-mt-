<?php

namespace App\Actions\Auth;

use App\Exceptions\SsoAuthenticationException;
use App\Models\Contact;
use App\Models\User;
use Laravel\Socialite\Two\User as SocialiteUser;

/**
 * Traduz a identidade devolvida pelo Keycloak em um User local.
 *
 * O Keycloak responde apenas "quem e" a pessoa. Nenhum papel ou permissao e
 * lido do token — a autorizacao vive inteiramente no Spatie, dentro do app.
 */
class ResolveKeycloakUserAction
{
    public function execute(SocialiteUser $ssoUser): User
    {
        $keycloakId = $ssoUser->getId();
        $email = strtolower(trim((string) $ssoUser->getEmail()));

        if ($email === '') {
            throw SsoAuthenticationException::missingEmail();
        }

        // Nao exigimos `email_verified`: no realm `defensoria` as contas sao
        // provisionadas pela TI (sem auto-cadastro), entao o flag vem `false`
        // por artefato do provisionamento e nao indica e-mail nao confiavel.
        // Se um dia o realm passar a permitir auto-registro, o pareamento por
        // e-mail abaixo vira risco de sequestro de conta e a checagem deve voltar.
        $name = trim((string) ($ssoUser->getName() ?: $ssoUser->getNickname())) ?: $email;

        // 1. Ja vinculado: o `sub` e a chave estavel, sobrevive a troca de e-mail.
        $user = User::where('keycloak_id', $keycloakId)->first();

        if ($user) {
            $user->forceFill(['name' => $name, 'email' => $email])->save();

            return $this->linkContact($user);
        }

        // 2. Primeiro login de alguem que ja tem conta local: vincula pelo e-mail.
        //    Preserva papeis e historico dos administradores que ja usavam senha.
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            $user->forceFill(['keycloak_id' => $keycloakId, 'name' => $name])->save();

            return $this->linkContact($user);
        }

        // 3. Rosto novo: provisiona inativo e sem papel nenhum, aguardando que um
        //    administrador libere o acesso e defina o que a pessoa pode fazer.
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'keycloak_id' => $keycloakId,
            'is_active' => false,
            'password' => null,
        ]);

        return $this->linkContact($user);
    }

    /**
     * Liga a conta ao cadastro de contato de mesmo e-mail, quando houver. E dai
     * que vem a lotacao e a responsabilidade por salas do setor — sem esse elo,
     * quem entra pelo SSO nao herda o que ja fazia como contato.
     */
    private function linkContact(User $user): User
    {
        $contact = Contact::whereRaw('LOWER(email) = ?', [strtolower($user->email)])
            ->whereNull('user_id')
            ->first();

        $contact?->update(['user_id' => $user->id]);

        return $user;
    }
}
