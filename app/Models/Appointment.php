<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
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
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A pet has many appointments
    public function pet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

}
