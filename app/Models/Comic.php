<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    protected $table = "comics";

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'author_id',
        'status',
        'view_count',
        'like_count',
        'is_premium',
        'deleted'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'comic_genres');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'comic_tags');
    }

    public function chapters()
    {
        return $this->hasMany(ComicChapter::class);
    }

    public function comments()
    {
        return $this->hasMany(ComicComment::class);
    }

    public function readingProgress()
    {
        return $this->hasMany(ComicReadingProgress::class);
    }
}
