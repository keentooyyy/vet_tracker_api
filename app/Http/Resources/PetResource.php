<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pet_type' => $this->pet_type_id->type ?? 'Unknown',
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'age' => $this->age,
        ];
    }
}
