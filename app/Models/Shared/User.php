<?php

namespace App\Models\Shared;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Shelter\Shelter;
use App\Models\Adopter\Adopter;
use App\Models\Rescuer\Rescuer;
use App\Models\Shared\Message;

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

    protected $appends = ['last_message', 'last_message_time'];

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

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getLastMessageAttribute()
    {
        // Return the decrypted message_content if set as a dynamic attribute, otherwise fallback to DB (encrypted)
        if (array_key_exists('last_message', $this->attributes)) {
            return $this->attributes['last_message'];
        }
        $message = \App\Models\Shared\Message::where(function ($q) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $this->user_id);
        })->orWhere(function ($q) {
            $q->where('sender_id', $this->user_id)
                ->where('receiver_id', auth()->id());
        })->orderByDesc('sent_at')->first();
        return $message?->message_content;
    }

    public function getLastMessageTimeAttribute()
    {
        if (array_key_exists('last_message_time', $this->attributes)) {
            return $this->attributes['last_message_time'];
        }
        $message = \App\Models\Shared\Message::where(function ($q) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $this->user_id);
        })->orWhere(function ($q) {
            $q->where('sender_id', $this->user_id)
                ->where('receiver_id', auth()->id());
        })->orderByDesc('sent_at')->first();
        return $message?->sent_at
            ? \Carbon\Carbon::parse($message->sent_at)->timezone('Asia/Manila')
            : null;
    }
    //Upload New Photo Feature
    public function getProfileImageAttribute()
    {
        $profilePic = \DB::table('user_profile_pic')
            ->where('user_id', $this->user_id)
            ->where('is_displayed', true)
            ->first();

        if ($profilePic && $profilePic->image_url) {
            // Check S3 first
            if (\Storage::disk('s3')->exists($profilePic->image_url)) {
                return \Storage::disk('s3')->url($profilePic->image_url);
            }
            // Then check public storage (local fallback)
            if (\Storage::disk('public')->exists($profilePic->image_url)) {
                return \Storage::disk('public')->url($profilePic->image_url);
            }
        }

        return asset('images/default-profile.png');
    }
}
