<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Business extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'archetype',
        'zone',
        'whatsapp',
        'primary_color',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owners(): HasMany
    {
        return $this->users()->where('role', 'owner');
    }

    public function staff(): HasMany
    {
        return $this->users()->where('role', 'staff');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
