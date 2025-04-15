<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Novel;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NovelController extends Controller
{
    public function index()
    {
        $novels = Novel::with(['genres', 'author'])
            ->where('author_id', Auth::user()->author->id)
            ->where('deleted', 0)
            ->get();

        return view('authordashboard.novels.index', compact('novels'));
    }

    public function show(Novel $novel)
    {
        if ($novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }

        $novel->load(['genres', 'tags', 'chapters', 'comments', 'author']);
        return view('authordashboard.novels.show', compact('novel'));
    }

    public function create()
    {
        $genres = Genre::all();
        $tags = Tag::all();
        return view('authordashboard.novels.create', compact('genres', 'tags'));
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
            
            $novel = new Novel();
            $novel->title = $request->title;
            $novel->description = $request->description;
            $novel->author_id = Auth::user()->author->id;
            $novel->status = 'en_cours'; 

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $novel->cover_image = '/storage/' . $request->file('cover_image')->storeAs('covers', $filename, 'public');
            } elseif ($request->unsplash_url) {
                $imageContent = file_get_contents($request->unsplash_url);
                $filename = time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('covers/' . $filename, $imageContent);
                $novel->cover_image = '/storage/covers/' . $filename;
            }

            $novel->save();

            $novel->genres()->attach($request->genres);
            if ($request->has('tags')) {
                $novel->tags()->attach($request->tags);
            }

            DB::commit();

            return redirect()
                ->route('authordashboard.novels.show', $novel)
                ->with('success', 'Roman créé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating novel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du roman.');
        }
    }

    public function edit(Novel $novel)
    {
        if ($novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }

        $genres = Genre::all();
        $tags = Tag::all();
        return view('authordashboard.novels.edit', compact('novel', 'genres', 'tags'));
    }

    public function update(Request $request, Novel $novel)
    {
        if ($novel->author_id !== Auth::user()->author->id) {
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

            $novel->title = $request->title;
            $novel->description = $request->description;
            $novel->status = $request->status;

            if ($request->hasFile('cover_image')) {
                if ($novel->cover_image) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $novel->cover_image));
                }
                $filename = time() . '_' . Str::random(10) . '.' . $request->file('cover_image')->getClientOriginalExtension();
                $novel->cover_image = '/storage/' . $request->file('cover_image')->storeAs('covers', $filename, 'public');
            } elseif ($request->filled('unsplash_url')) {
                if ($novel->cover_image) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $novel->cover_image));
                }
                $imageContent = file_get_contents($request->unsplash_url);
                $filename = time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('covers/' . $filename, $imageContent);
                $novel->cover_image = '/storage/covers/' . $filename;
            }

            $novel->save();

            $novel->genres()->sync($request->genres);
            $novel->tags()->sync($request->tags ?? []);

            DB::commit();

            return redirect()
                ->route('authordashboard.novels.show', $novel)
                ->with('success', 'Roman mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Une erreur est survenue lors de la mise à jour du roman', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('Une erreur est survenue lors de la mise à jour du roman');
        }
    }

    public function destroy(Novel $novel)
    {
        if ($novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }

        $novel->update(['deleted' => 1]);

        return redirect()
            ->route('authordashboard.novels')
            ->with('success', 'Roman supprimé avec succès!');
    }
    
    public function read(Novel $novel)
    {
        if ($novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }

        $novel->load(['chapters' => function($query) {
            $query->orderBy('chapter_number', 'asc');
        }]);
        
        return view('authordashboard.novels.read', compact('novel'));
    }
}
