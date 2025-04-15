<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovelChapterComment extends Model
{
    protected $table = "novel_chapter_comments";
    
    protected $fillable = [
        'user_id',
        'chapter_id',
        'content',
    ];

    public function chapter()
    {
        return $this->belongsTo(NovelChapter::class, 'chapter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
