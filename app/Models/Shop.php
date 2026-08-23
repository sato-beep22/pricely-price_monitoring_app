<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'description',
        'photo_path',
        'is_active',
        'classification',
        'views',
    ];

    /**
     * Get the full public URL for the shop photo, or null if not set.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return '/shop_photos/'.$this->photo_path;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the buyer who owns this shop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all price records for this shop.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Scope to only active shops.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find shops within a radius (in km) of a coordinate.
     * Uses the Haversine formula approximation.
     */
    public function scopeNearby(Builder $query, float $latitude, float $longitude, float $radiusKm = 50): Builder
    {
        return $query->selectRaw(
            '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }
}
