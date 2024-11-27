<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PetResource extends JsonResource
{
    public function toArray($request)
    {
        $birthdate = Carbon::parse($this->birthdate);
        $age = round($birthdate->diffInYears(Carbon::now()));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $age,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'type' => $this->petType ? $this->petType->type : 'Unknown',
            'breed' => $this->breed,
        ];
    }
}
