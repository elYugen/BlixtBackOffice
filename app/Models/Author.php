<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = "authors";

    protected $fillable = [
        'user_id',
        'pen_name',
        'avatar',
        'bio',
        'deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function novels()
    {
        return $this->hasMany(Novel::class);
    }

    public function comics()
    {
        return $this->hasMany(Comic::class);
    }
}
