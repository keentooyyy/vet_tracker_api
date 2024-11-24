<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appoinment extends Model
{
    //
    protected $fillable = [
        'user_id',
        'pet_id',
        'start_time',
        'end_time',
        'purpose',
        'appointment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
