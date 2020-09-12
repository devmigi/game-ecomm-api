<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = [
            'name' => $this->name,
            'email' => $this->email,
            'member_since' => $this->created_at->format('Y-m-d H:i:s'),
        ];

        return $user;
    }
}
