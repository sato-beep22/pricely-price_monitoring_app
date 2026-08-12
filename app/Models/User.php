<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'pin_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',
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
            'phone_verified_at' => 'datetime',
            'phone_verification_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if the user's phone is verified.
     */
    public function phoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Determine if the user is a buyer.
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Determine if the user is a farmer.
     */
    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    /**
     * Get the shop owned by this buyer.
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Get subscriptions where this user is the farmer.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'farmer_id');
    }

    /**
     * Get subscriptions where this user is the buyer being subscribed to.
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, 'buyer_id');
    }

    /**
     * Get ceiling prices set by this admin.
     */
    public function ceilingPrices(): HasMany
    {
        return $this->hasMany(CeilingPrice::class, 'admin_id');
    }
}
