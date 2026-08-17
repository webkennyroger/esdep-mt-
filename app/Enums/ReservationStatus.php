<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::AUTHORIZED => 'Autorizada',
            self::REJECTED => 'Rejeitada',
        };
    }
}
