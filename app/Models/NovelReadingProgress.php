<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovelReadingProgress extends Model
{
    protected $table = "novel_reading_progess";

    protected $fillable = [
        'user_id',
        'novel_id',
        'last_read_chapter_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    public function lastReadChapter()
    {
        return $this->belongsTo(NovelChapter::class, 'last_read_chapter_id');
    }
}
