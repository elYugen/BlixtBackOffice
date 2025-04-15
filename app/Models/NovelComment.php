<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovelComment extends Model
{

    protected $table = "novel_comments";

    protected $fillable = [
        'user_id',
        'novel_id',
        'content',
    ];

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
