<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovelChapter extends Model
{
    protected $table = "novel_chapters";

    protected $fillable = [
        'novel_id',
        'title',
        'chapter_number',
        'content',
        'view_count',
        'like_count',
        'is_premium',
        'deleted'
    ];

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    public function comments()
    {
        return $this->hasMany(NovelChapterComment::class, 'chapter_id');
    }
}
