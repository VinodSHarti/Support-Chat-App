<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagesOutbox extends Model
{
    protected $fillable = [
        'message_id',
        'delivered',
    ];
}
