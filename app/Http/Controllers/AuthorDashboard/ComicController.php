<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Genre;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ComicController extends Controller
{
    public function index()
    {
        $comics = Comic::with(['genres', 'author'])
        ->where('author_id', Auth::user()->author->id)
        ->where('deleted', 0)
        ->get();

    return view('authordashboard.comics.index', compact('comics'));
    }

    public function show(Comic $comic)
    {
        if ($comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }

        $comic->load(['genres', 'tags', 'chapters', 'comments', 'author']);
        return view('authordashboard.comics.show', compact('comic'));
    }

    public function create()
    {
        $genres = Genre::all();
        $tags = Tag::all();
        return view('authordashboard.comics.create', compact('genres', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'cover_image' => 'required_without:unsplash_url|image|mimes:jpeg,png,jpg|max:2048',
            'unsplash_url' => 'required_without:cover_image|nullable|url',
        ]);

        try {
            DB::beginTransaction();
            
            $comic = new Comic();
            $comic->title = $request->title;
            $comic->description = $request->description;
            $comic->author_id = Auth::user()->author->id;
            $comic->status = 'en_cours'; 

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $comic->cover_image = '/storage/' . $request->file('cover_image')->storeAs('covers', $filename, 'public');
            } elseif ($request->unsplash_url) {
                $imageContent = file_get_contents($request->unsplash_url);
                $filename = time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('covers/' . $filename, $imageContent);
                $comic->cover_image = '/storage/covers/' . $filename;
            }

            $comic->save();

            $comic->genres()->attach($request->genres);
            if ($request->has('tags')) {
                $comic->tags()->attach($request->tags);
            }

            DB::commit();

            return redirect()
                ->route('authordashboard.comics.show', $comic)
                ->with('success', 'Bande dessinée créé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating comic', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la bande dessinée.');
        }
    }

    public function edit(Comic $comic)
    {
        if ($comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }

        $genres = Genre::all();
        $tags = Tag::all();
        return view('authordashboard.comics.edit', compact('comic', 'genres', 'tags'));
    }

    public function update(Request $request, Comic $comic)
    {
        if ($comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:en_cours,terminé,annulé,en_pause', 
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'unsplash_url' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            $comic->title = $request->title;
            $comic->description = $request->description;
            $comic->status = $request->status;

            if ($request->hasFile('cover_image')) {
                if ($comic->cover_image) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $comic->cover_image));
                }
                $filename = time() . '_' . Str::random(10) . '.' . $request->file('cover_image')->getClientOriginalExtension();
                $comic->cover_image = '/storage/' . $request->file('cover_image')->storeAs('covers', $filename, 'public');
            } elseif ($request->filled('unsplash_url')) {
                if ($comic->cover_image) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $comic->cover_image));
                }
                $imageContent = file_get_contents($request->unsplash_url);
                $filename = time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('covers/' . $filename, $imageContent);
                $comic->cover_image = '/storage/covers/' . $filename;
            }

            $comic->save();

            $comic->genres()->sync($request->genres);
            $comic->tags()->sync($request->tags ?? []);

            DB::commit();

            return redirect()
                ->route('authordashboard.comics.show', $comic)
                ->with('success', 'Comics mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Une erreur est survenue lors de la mise à jour du comics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('Une erreur est survenue lors de la mise à jour du roman');
        }
    }

    public function destroy(Comic $comic)
    {
        if ($comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }

        $comic->update(['deleted' => 1]);

        return redirect()
            ->route('authordashboard.comics')
            ->with('success', 'Comics supprimé avec succès!');
    }
    
    public function read(Comic $comic)
    {
        // Check if the current user is the author of the comic
        if ($comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }
        
        // Load the comic with its chapters and pages
        $comic->load([
            'chapters' => function($query) {
                $query->orderBy('chapter_number', 'asc');
            }, 
            'chapters.pages' => function($query) {
                $query->orderBy('page_number', 'asc');
            }
        ]);
        
        // Debug information
        if (config('app.debug')) {
            Log::info('Comic read view loaded', [
                'comic_id' => $comic->id,
                'chapters_count' => $comic->chapters->count(),
                'pages_count' => $comic->chapters->sum(function($chapter) {
                    return $chapter->pages->count();
                })
            ]);
        }
        
        return view('authordashboard.comics.read', compact('comic'));
    }
}
