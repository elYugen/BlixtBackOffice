<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicChapterPage extends Model
{
    protected $table = "comic_chapter_pages";

    protected $fillable = [
        'chapter_id',
        'page_number',
        'image_url',
        'caption'
    ];

    public function chapter()
    {
        return $this->belongsTo(ComicChapter::class, 'chapter_id');
    }
}
