<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    protected $fillable = ['pet_id', 'image_url'];

    public function pet()
    {
        return $this->belongsTo(\App\Models\Shared\Pet::class, 'pet_id', 'pet_id');
    }
}
