<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'type',
        'status',
        'provider',
        'message_code',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];
}
