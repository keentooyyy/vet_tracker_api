<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetMedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pet_name' => $this->pet->name,
            'vaccine_name' => $this->vaccine->name,
            'date_of_administration' => $this->date_of_administration,
            'date_of_next_administration' => $this->date_of_next_administration,
        ];
    }
}
