<?php

namespace App\Models\Rescuer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\User;

class Rescuer extends Model
{
    use HasFactory;

    protected $primaryKey = 'rescuer_id';

    protected $fillable = [
        'user_id',
        'organization_name',
        'location',
        'verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
} 