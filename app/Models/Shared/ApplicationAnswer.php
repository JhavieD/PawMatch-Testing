<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationAnswer extends Model
{
    use HasFactory;

    protected $primaryKey = 'answer_id';

    protected $fillable = [
        'application_id', 'question', 'answer'
    ];

    public function application() { return $this->belongsTo(AdoptionApplication::class, 'application_id'); }
} 