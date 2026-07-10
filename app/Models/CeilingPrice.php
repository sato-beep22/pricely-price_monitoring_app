<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CeilingPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'crop_id',
        'admin_id',
        'specification',
        'max_price',
        'effective_date',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_price' => 'decimal:2',
            'effective_date' => 'date',
        ];
    }

    /**
     * Get the crop this ceiling price applies to.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the admin who set this ceiling price.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope to get the current ceiling price for a crop.
     */
    public function scopeCurrentForCrop(Builder $query, int $cropId): Builder
    {
        return $query->where('crop_id', $cropId)
            ->where('effective_date', '<=', now())
            ->orderByDesc('effective_date');
    }
}
