<?php

namespace App\Models\Shared;

use App\Models\Shelter\Shelter;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'shelter_verifications';

    protected $fillable = [
        'shelter_id',
        'status',
        'document_path',
    ];

    public function shelter()
    {
        // return $this->belongsTo(User::class, 'shelter_id');
        return $this->belongsTo(Shelter::class, 'shelter_id');
    }
}


