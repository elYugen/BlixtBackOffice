<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'deleted'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function author() 
    {
        return $this->hasOne(Author::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Novel::class, 'user_favorites');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function novelReadingProgress()
    {
        return $this->hasMany(NovelReadingProgress::class);
    }

    public function comicReadingProgress()
    {
        return $this->hasMany(ComicReadingProgress::class);
    }

    public function novelComments()
    {
        return $this->hasMany(NovelComment::class);
    }

    public function comicComments()
    {
        return $this->hasMany(ComicComment::class);
    }

    public function novelChapterComments()
    {
        return $this->hasMany(NovelChapterComment::class);
    }

    public function comicChapterComments()
    {
        return $this->hasMany(ComicChapterComment::class);
    }
}
