<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    protected $table = "novels";

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
        return $this->belongsToMany(Genre::class, 'novel_genres');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'novel_tags');
    }

    public function chapters()
    {
        return $this->hasMany(NovelChapter::class);
    }

    public function comments()
    {
        return $this->hasMany(NovelComment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'user_favorites');
    }

    public function readingProgress()
    {
        return $this->hasMany(NovelReadingProgress::class);
    }
}
