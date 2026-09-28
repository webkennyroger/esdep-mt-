<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Falha no login via SSO que deve ser apresentada ao usuario na tela de login.
 * A mensagem e sempre segura para exibicao (nao vaza detalhes do token).
 */
class SsoAuthenticationException extends RuntimeException
{
    public static function missingEmail(): self
    {
        return new self('Sua conta institucional não possui e-mail cadastrado. Procure o suporte de TI.');
    }

    public static function providerFailure(): self
    {
        return new self('Não foi possível concluir o login institucional. Tente novamente.');
    }
}
