<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = "tags";

    protected $fillable = [
        'name',
    ];

    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'novel_tags');
    }

    public function comics()
    {
        return $this->belongsToMany(Comic::class, 'comic_tags');
    }
}
