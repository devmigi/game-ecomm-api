<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id');
    }
}
