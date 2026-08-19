<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_contact_group')->withPivot('is_responsible');
    }

    public function responsibleContacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_contact_group')
            ->wherePivot('is_responsible', true)
            ->withPivot('is_responsible');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
