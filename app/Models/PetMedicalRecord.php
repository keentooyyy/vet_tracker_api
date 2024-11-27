<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetMedicalRecord extends Model
{
    //
    protected $fillable = [
        'pet_id',
        'date_of_administration',
        'vaccine',
        'date_of_next_administration'

    ];

}
