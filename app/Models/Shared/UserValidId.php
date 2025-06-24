<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class UserValidId extends Model
{
    protected $table = 'user_valid_ids';

    protected $fillable = [
        'user_id',
        'image_url',
        'file_type',
        'uploaded_at',
    ];

    public $timestamps = false; // Set to true if you have created_at/updated_at columns
}