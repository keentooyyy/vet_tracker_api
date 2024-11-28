<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'pet_type_id',
        'user_id',
        'name',
        'breed',
        'birthdate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function petType()
    {
        return $this->belongsTo(PetType::class);
    }

    public function medicalRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PetMedicalRecord::class);
    }

}
