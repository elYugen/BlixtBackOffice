<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = "genres";

    protected $fillable = [
        'name',
    ];

    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'novel_genres');
    }

    public function comics()
    {
        return $this->belongsToMany(Comic::class, 'comic_genres');
    }
}
