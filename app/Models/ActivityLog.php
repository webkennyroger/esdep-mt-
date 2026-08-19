<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ActivityLog extends Model
{
    protected $fillable = [
        'action',
        'description',
        'subject_type',
        'subject_id',
        'user_id',
        'changes',
        'metadata',
    ];

    protected $casts = [
        'changes' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Accessors para exibicao

    protected function actionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) {
                'created' => 'Criacao',
                'updated' => 'Atualizacao',
                'deleted' => 'Exclusao',
                'approved' => 'Aprovacao',
                'rejected' => 'Rejeicao',
                'reopened' => 'Reabertura',
                'activated' => 'Ativacao',
                'deactivated' => 'Desativacao',
                'login' => 'Login',
                'logout' => 'Logout',
                default => ucfirst($this->action),
            },
        );
    }

    protected function actionColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) {
                'created' => 'emerald',
                'updated' => 'blue',
                'deleted' => 'red',
                'approved' => 'emerald',
                'rejected' => 'red',
                'reopened' => 'amber',
                'activated' => 'emerald',
                'deactivated' => 'amber',
                'login' => 'purple',
                'logout' => 'slate',
                default => 'slate',
            },
        );
    }

    protected function actionIcon(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) {
                'created' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                'updated' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                'deleted' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                'approved' => 'M5 13l4 4L19 7',
                'rejected' => 'M6 18L18 6M6 6l12 12',
                'reopened' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                'activated' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'deactivated' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                'login' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
                'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
                default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            },
        );
    }

    protected function subjectLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->subject_type) {
                'App\Models\Reservation' => 'Reserva',
                'App\Models\User' => 'Usuario',
                'App\Models\Room' => 'Sala',
                'App\Models\Contact' => 'Contato',
                'App\Models\ContactGroup' => 'Grupo de Contatos',
                default => class_basename($this->subject_type ?? ''),
            },
        );
    }
}
