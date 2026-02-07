<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language_preference',
        'profile_pic',
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
        ];
    }

    /**
     * Always store password as bcrypt hash in DB (plain text never saved).
     * If value is already a bcrypt hash, store as-is to avoid double-hashing.
     */
    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $isBcrypt = preg_match('/^\$2[ayx]\$\d{2}\$/', $value) === 1;
        $this->attributes['password'] = $isBcrypt ? $value : Hash::make($value);
    }

    /** Profile picture URL; default avatar when not set. */
    public function getProfilePicUrlAttribute(): string
    {
        if (! empty($this->profile_pic)) {
            return asset('storage/' . $this->profile_pic);
        }
        return asset('images/default-avatar.svg');
    }

    /** @return HasMany<Booking, $this> */
    public function createdBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    /** Guest profile linked to this user (portal use). */
    public function guest(): HasOne
    {
        return $this->hasOne(Guest::class);
    }
}
