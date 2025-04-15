<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicChapter extends Model
{
    protected $table = "comic_chapters";

    protected $fillable = [
        'comic_id',
        'title',
        'chapter_number',
        'view_count',
        'like_count',
        'is_premium',
        'deleted'
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function comments()
    {
        return $this->hasMany(ComicChapterComment::class, 'chapter_id');
    }

    public function pages()
    {
        return $this->hasMany(ComicChapterPage::class, 'chapter_id');
    }
}
