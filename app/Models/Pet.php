<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'pet_type_id',
        'user_id',
        'name',
        'age',
        'weight',
        'breed',
        'color',
    ];

    public function petType()
    {
        return $this->belongsTo(PetType::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function medicalRecords(){
        return $this->hasMany(PetMedicalRecord::class);
    }
}
