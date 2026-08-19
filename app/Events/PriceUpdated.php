<?php

namespace App\Events;

use App\Models\Shop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Shop $shop;

    public array $updates;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, array $updates)
    {
        $this->shop = $shop;
        $this->updates = $updates;
    }
}
