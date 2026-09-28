<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $action,
        string $description,
        ?object $subject = null,
        ?array $changes = null,
        ?array $metadata = null
    ): ActivityLog {
        return ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'user_id' => Auth::guard('web')->id(),
            'changes' => $changes,
            'metadata' => $metadata,
        ]);
    }

    public static function created(object $subject, ?string $description = null): ActivityLog
    {
        $modelName = class_basename(get_class($subject));
        $desc = $description ?? "{$modelName} criado(a) com sucesso";

        return self::log('created', $desc, $subject, $subject->toArray());
    }

    public static function updated(object $subject, array $oldValues, array $newValues, ?string $description = null): ActivityLog
    {
        $modelName = class_basename(get_class($subject));
        $desc = $description ?? "{$modelName} atualizado(a)";

        $changes = [];
        foreach ($newValues as $key => $value) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $value,
                ];
            }
        }

        return self::log('updated', $desc, $subject, $changes ?: $newValues);
    }

    public static function deleted(object $subject, ?string $description = null): ActivityLog
    {
        $modelName = class_basename(get_class($subject));
        $desc = $description ?? "{$modelName} excluído(a)";

        return self::log('deleted', $desc, $subject, $subject->toArray());
    }

    public static function reservationCreated(object $reservation): ActivityLog
    {
        return self::log(
            'created',
            "Reserva \"{$reservation->event_name}\" solicitada por {$reservation->requester_name}",
            $reservation,
            [
                'event_name' => $reservation->event_name,
                'date' => $reservation->date->format('d/m/Y'),
                'room' => $reservation->room?->name,
            ]
        );
    }

    public static function reservationApproved(object $reservation): ActivityLog
    {
        return self::log(
            'approved',
            "Reserva \"{$reservation->event_name}\" aprovada",
            $reservation,
            ['status' => ['old' => 'pending', 'new' => 'authorized']]
        );
    }

    public static function reservationRejected(object $reservation, ?string $reason = null): ActivityLog
    {
        return self::log(
            'rejected',
            "Reserva \"{$reservation->event_name}\" rejeitada".($reason ? ": {$reason}" : ''),
            $reservation,
            ['status' => ['old' => 'pending', 'new' => 'rejected'], 'reason' => $reason]
        );
    }

    public static function reservationUpdated(object $reservation, array $changes): ActivityLog
    {
        $labels = [
            'event_name' => ['Nome do evento', 'alterado'],
            'description' => ['Descrição', 'alterada'],
            'date' => ['Data', 'alterada'],
            'start_time' => ['Horário inicial', 'alterado'],
            'end_time' => ['Horário final', 'alterado'],
            'room_id' => ['Sala', 'alterada'],
            'requester_name' => ['Solicitante', 'alterado'],
            'requester_email' => ['Email do solicitante', 'alterado'],
            'attendees_count' => ['Número de participantes', 'alterado'],
            'coffee_break' => ['Coffee break', 'alterado'],
            'sonorizacao' => ['Sonorização', 'alterada'],
            'transmissao' => ['Transmissão', 'alterada'],
            'projetor' => ['Projetor', 'alterado'],
        ];

        $descriptions = [];

        $roomNames = isset($changes['room_id'])
            ? Room::whereIn('id', array_filter([(int) $changes['room_id'], (int) $reservation->room_id]))->pluck('name', 'id')
            : collect();

        foreach ($changes as $field => $oldValue) {
            $newValue = $reservation->{$field};

            // So gera descricao para campos que realmente mudaram
            if (self::normalizeReservationValue($field, $oldValue) == self::normalizeReservationValue($field, $newValue)) {
                continue;
            }

            [$label, $verb] = $labels[$field] ?? [ucfirst(str_replace('_', ' ', $field)), 'alterado'];
            $oldLabel = self::formatReservationValue($field, $oldValue, $roomNames);
            $newLabel = self::formatReservationValue($field, $newValue, $roomNames);

            $descriptions[] = "{$label} {$verb} de \"{$oldLabel}\" para \"{$newLabel}\"";
        }

        $desc = count($descriptions) > 0
            ? "Reserva \"{$reservation->event_name}\": ".implode('; ', $descriptions)
            : "Reserva \"{$reservation->event_name}\" atualizada";

        return self::log(
            'updated',
            $desc,
            $reservation,
            $changes
        );
    }

    private static function normalizeReservationValue(string $field, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($field) {
            'date' => $value instanceof Carbon
                ? $value->format('Y-m-d')
                : Carbon::parse($value)->format('Y-m-d'),
            'coffee_break', 'sonorizacao', 'transmissao', 'projetor' => (bool) $value,
            'attendees_count' => (int) $value,
            default => $value,
        };
    }

    private static function formatReservationValue(string $field, mixed $value, ?Collection $roomNames = null): string
    {
        if ($value === null || $value === '') {
            return 'não definido';
        }

        return match ($field) {
            'room_id' => $roomNames?->get((int) $value) ?? 'Sala #'.$value,
            'date' => $value instanceof Carbon
                ? $value->format('d/m/Y')
                : Carbon::parse($value)->format('d/m/Y'),
            'coffee_break', 'sonorizacao', 'transmissao', 'projetor' => $value ? 'Sim' : 'Não',
            'start_time', 'end_time' => substr((string) $value, 0, 5),
            default => (string) $value,
        };
    }

    public static function userUpdated(object $user, array $oldValues, array $newValues): ActivityLog
    {
        return self::log(
            'updated',
            "Usuário \"{$user->name}\" atualizado",
            $user,
            $newValues
        );
    }

    public static function userDeleted(object $user): ActivityLog
    {
        return self::log(
            'deleted',
            "Usuário \"{$user->name}\" excluído",
            $user,
            ['name' => $user->name, 'email' => $user->email]
        );
    }

    public static function roomCreated(object $room): ActivityLog
    {
        return self::log(
            'created',
            "Sala \"{$room->name}\" criada",
            $room,
            ['name' => $room->name, 'capacity' => $room->capacity]
        );
    }

    public static function roomUpdated(object $room, array $changes): ActivityLog
    {
        return self::log(
            'updated',
            "Sala \"{$room->name}\" atualizada",
            $room,
            $changes
        );
    }

    public static function roomDeleted(object $room): ActivityLog
    {
        return self::log(
            'deleted',
            "Sala \"{$room->name}\" excluída",
            $room,
            ['name' => $room->name]
        );
    }
}
