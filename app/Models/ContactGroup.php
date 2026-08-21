<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_contact_id',
        'sort_order',
        'description',
        'color',
    ];

    protected $casts = ['sort_order' => 'integer'];

    public function ownerContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'owner_contact_id');
    }

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

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
