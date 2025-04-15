<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicComment extends Model
{
    protected $table = "comic_comments";
    
    protected $fillable = [
        'user_id',
        'comic_id',
        'content',
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
