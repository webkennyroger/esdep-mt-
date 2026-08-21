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
            'Conflito de horario: Esta sala ja possui uma reserva agendada '.
            'no mesmo periodo. Escolha outro horario.'
        );
    }
}
