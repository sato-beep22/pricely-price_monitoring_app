<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CeilingPriceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  array<int, array{crop_name: string, specification: string|null, old_max_price: string|null, new_max_price: string}>  $updates
     */
    public function __construct(public readonly array $updates) {}
}
