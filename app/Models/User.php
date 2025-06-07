<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function shelter()
    {
        return $this->hasOne(Shelter::class, 'user_id', 'user_id');
    }

    public function adopter()
    {
        return $this->hasOne(Adopter::class, 'user_id', 'user_id');
    }

    public function rescuer()
    {
        return $this->hasOne(Rescuer::class, 'user_id', 'user_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isShelter()
    {
        return $this->role === 'shelter';
    }

    public function isAdopter()
    {
        return $this->role === 'adopter';
    }

    public function favoritePets()
    {
        return $this->hasOne(Adopter::class, 'user_id', 'user_id')
            ->with('savedPets');
    }

    public function adopterApplications()
    {
        return $this->hasOne(Adopter::class, 'user_id', 'user_id')->with('applications');
    }

    public function adopterProfile()
    {
        return $this->hasOne(Adopter::class, 'user_id', 'user_id');
    }

    public function shelterProfile()
    {
        return $this->hasOne(Shelter::class, 'user_id', 'user_id');
    }

    public function rescuerProfile()
    {
        return $this->hasOne(Rescuer::class, 'user_id', 'user_id');
    }
}
