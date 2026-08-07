<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shop_id',
        'crop_id',
        'specification',
        'price_per_kg',
        'recorded_at',
        'source',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * Get the shop this price belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the crop this price is for.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Scope to filter prices within a date range.
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('recorded_at', [$from, $to]);
    }

    /**
     * Scope to get prices for a specific crop.
     */
    public function scopeForCrop(Builder $query, int $cropId): Builder
    {
        return $query->where('crop_id', $cropId);
    }
}
