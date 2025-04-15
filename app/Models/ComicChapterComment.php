<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicChapterComment extends Model
{
    protected $table = "comic_chapter_comments";
    
    protected $fillable = [
        'user_id',
        'chapter_id',
        'content',
    ];

    public function chapter()
    {
        return $this->belongsTo(ComicChapter::class, 'chapter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
