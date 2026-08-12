<?php

namespace App\Events;

use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Shop $shop;

    public Crop $crop;

    public Price $price;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, Crop $crop, Price $price)
    {
        $this->shop = $shop;
        $this->crop = $crop;
        $this->price = $price;
    }
}
