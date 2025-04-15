<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = "subscriptions";

    protected $fillable = [
        'user_id',
        'plan',
        'price',
        'starts_at',
        'ends_at',
        'is_active',
        'auto_renew',
        'canceled_at',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
