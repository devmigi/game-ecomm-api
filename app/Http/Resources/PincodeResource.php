<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PincodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pincode' => $this->pincode,
            'location' => $this->location,
            'city' => $this->city->name,
            'latitude' => $this->city->latitude,
            'longitude' => $this->city->longitude,
            'state' => $this->city->state->name,
        ];
    }
}
