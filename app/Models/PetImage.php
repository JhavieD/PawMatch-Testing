<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    protected $fillable = ['pet_id', 'image_url'];

    public function pet()
    {
        return $this->belongsTo(\App\Models\Pet::class, 'pet_id', 'pet_id');
    }
}
