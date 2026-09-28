<?php

namespace App\Exceptions;

use DomainException;

/**
 * Lancada quando existe conflito de horario entre reservas no mesmo espaco.
 */
class ConflictException extends DomainException
{
    public static function forRoom(int $roomId): self
    {
        return new self(
            'Conflito de horário: Esta sala já possui uma reserva agendada '.
            'no mesmo período. Escolha outro horário.'
        );
    }
}
