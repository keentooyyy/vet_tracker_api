<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetMedicalRecord extends Model
{
    //
    protected $fillable = [
        'pet_id',
        'date_of_administration',
        'vaccine_id',
        'date_of_next_administration'
    ];

    public function pet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
    public function vaccine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(VaccineType::class);
    }
}
