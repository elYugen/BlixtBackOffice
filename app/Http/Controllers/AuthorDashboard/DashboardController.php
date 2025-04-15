<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\Novel;
use App\Models\NovelChapter;
use App\Models\Comic;
use App\Models\ComicChapter;
use App\Models\Genre;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $author = Auth::user()->author;

        $stats = [
            // Novel stats
            'novels_count' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->count(),
                
            'novel_chapters_count' => NovelChapter::whereHas('novel', function($query) use ($author) {
                $query->where('author_id', $author->id)
                    ->where('deleted', 0);
            })->count(),
            
            'novel_views' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('view_count'),
                
            'novel_likes' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('like_count'),
                
            'novel_comments' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->withCount('comments')
                ->get()
                ->sum('comments_count'),

            // Comic stats
            'comics_count' => Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->count(),
                
            'comic_chapters_count' => ComicChapter::whereHas('comic', function($query) use ($author) {
                $query->where('author_id', $author->id)
                    ->where('deleted', 0);
            })->count(),
            
            'comic_views' => Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('view_count'),
                
            'comic_likes' => Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('like_count'),
                
            'comic_comments' => Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->withCount('comments')  // Changed from 'comicComments' to 'comments'
                ->get()
                ->sum('comments_count'),

            // Total stats
            'total_views' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('view_count') +
                Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('view_count'),
                
            'total_likes' => Novel::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('like_count') +
                Comic::where('author_id', $author->id)
                ->where('deleted', 0)
                ->sum('like_count'),
        ];

        // Get genres for both novels and comics
        $topGenres = Genre::select('genres.name', DB::raw('COUNT(*) as count'))
            ->leftJoin('novel_genres', 'genres.id', '=', 'novel_genres.genre_id')
            ->leftJoin('novels', function($join) use ($author) {
                $join->on('novels.id', '=', 'novel_genres.novel_id')
                    ->where('novels.author_id', $author->id)
                    ->where('novels.deleted', 0);
            })
            ->leftJoin('comic_genres', 'genres.id', '=', 'comic_genres.genre_id')
            ->leftJoin('comics', function($join) use ($author) {
                $join->on('comics.id', '=', 'comic_genres.comic_id')
                    ->where('comics.author_id', $author->id)
                    ->where('comics.deleted', 0);
            })
            ->havingRaw('COUNT(*) > 0')
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Get tags for both novels and comics
        $topTags = Tag::select('tags.name', DB::raw('COUNT(*) as count'))
            ->leftJoin('novel_tags', 'tags.id', '=', 'novel_tags.tag_id')
            ->leftJoin('novels', function($join) use ($author) {
                $join->on('novels.id', '=', 'novel_tags.novel_id')
                    ->where('novels.author_id', $author->id)
                    ->where('novels.deleted', 0);
            })
            ->leftJoin('comic_tags', 'tags.id', '=', 'comic_tags.tag_id')
            ->leftJoin('comics', function($join) use ($author) {
                $join->on('comics.id', '=', 'comic_tags.comic_id')
                    ->where('comics.author_id', $author->id)
                    ->where('comics.deleted', 0);
            })
            ->havingRaw('COUNT(*) > 0')
            ->groupBy('tags.id', 'tags.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('authordashboard.stats', compact('stats', 'topGenres', 'topTags'));
    }
}
