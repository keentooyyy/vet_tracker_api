<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineType extends Model
{
    //
    protected $fillable = [
        'type'
    ];

    public function medicalRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PetMedicalRecord::class);
    }
}