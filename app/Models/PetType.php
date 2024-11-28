<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetType extends Model
{
    protected $fillable = [
        'type'
    ];

    public function pets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
