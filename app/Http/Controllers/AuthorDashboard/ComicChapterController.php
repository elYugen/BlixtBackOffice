<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\ComicChapter;
use App\Models\ComicChapterPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComicChapterController extends Controller
{
    public function create(Request $request)
    {
        $comics = Comic::where('author_id', Auth::user()->author->id)
            ->where('deleted', 0)
            ->get();

        $selectedComic = null;
        $usedNumbers = [];
        
        if ($request->has('comic_id')) {
            $selectedComic = Comic::where('id', $request->comic_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();
                
            $usedNumbers = ComicChapter::where('comic_id', $selectedComic->id)
                ->pluck('chapter_number')
                ->toArray();
        }

        return view('authordashboard.comics.chapter_create', compact('comics', 'selectedComic', 'usedNumbers'));
    }

    public function store(Request $request)
    {
        Log::info('Starting comic chapter creation', ['request' => $request->all()]);

        try {
            $comic = Comic::where('id', $request->comic_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();

            Log::info('Comic found', ['comic_id' => $comic->id]);

            $request->validate([
                'comic_id' => 'required|exists:comics,id',
                'title' => 'required|string|max:255',
                'chapter_number' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = ComicChapter::where('comic_id', $request->comic_id)
                            ->where('chapter_number', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Ce numéro de chapitre est déjà utilisé.');
                        }
                    },
                ],
                'pages' => 'required|array|min:1',
                'pages.*.image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'pages.*.caption' => 'nullable|string|max:255',
                'pages.*.page_number' => 'required|integer|min:1',
            ]);

            Log::info('Validation passed');

            DB::beginTransaction();

            // Créer le chapitre
            $chapter = ComicChapter::create([
                'comic_id' => $request->comic_id,
                'title' => $request->title,
                'chapter_number' => $request->chapter_number,
                'view_count' => 0,
                'like_count' => 0,
                'is_premium' => false,
                'deleted' => false
            ]);

            Log::info('Chapter created', ['chapter_id' => $chapter->id]);

            // Traiter chaque page
            foreach ($request->pages as $pageData) {
                if (isset($pageData['image'])) {
                    $image = $pageData['image'];
                    $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('comics/chapters/' . $chapter->id, $imageName, 'public');
                    $imageUrl = '/storage/comics/chapters/' . $chapter->id . '/' . $imageName;

                    ComicChapterPage::create([
                        'chapter_id' => $chapter->id,
                        'page_number' => $pageData['page_number'],
                        'image_url' => $imageUrl,
                        'caption' => $pageData['caption'] ?? null
                    ]);

                    Log::info('Page created', [
                        'chapter_id' => $chapter->id,
                        'page_number' => $pageData['page_number']
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('authordashboard.comics.show', $comic)
                ->with('success', 'Chapitre créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating comic chapter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du chapitre: ' . $e->getMessage());
        }
    }

    public function getUsedNumbers($comic_id)
    {
        try {
            $comic = Comic::where('id', $comic_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();

            $usedNumbers = ComicChapter::where('comic_id', $comic->id)
                ->orderBy('chapter_number')
                ->pluck('chapter_number')
                ->toArray();

            return response()->json($usedNumbers);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Bande dessinée non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error('Error getting used chapter numbers: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue'], 500);
        }
    }
        
    public function edit(ComicChapter $chapter)
    {
        // Vérifier que l'auteur est bien le propriétaire du chapitre
        if ($chapter->comic->author_id !== Auth::user()->author->id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Charger les pages du chapitre
        $chapter->load('pages');
    
        // Récupérer les numéros de chapitre déjà utilisés
        $usedNumbers = ComicChapter::where('comic_id', $chapter->comic_id)
            ->where('id', '!=', $chapter->id)
            ->pluck('chapter_number')
            ->toArray();
    
        return view('authordashboard.comics.chapter_edit', compact('chapter', 'usedNumbers'));
    }
    
    public function update(Request $request, ComicChapter $chapter)
    {
        Log::info('Starting comic chapter update', ['chapter_id' => $chapter->id]);
    
        try {
            // Vérifier que l'auteur est bien le propriétaire du chapitre
            if ($chapter->comic->author_id !== Auth::user()->author->id) {
                abort(403, 'Unauthorized action.');
            }
    
            $request->validate([
                'title' => 'required|string|max:255',
                'chapter_number' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request, $chapter) {
                        $exists = ComicChapter::where('comic_id', $chapter->comic_id)
                            ->where('chapter_number', $value)
                            ->where('id', '!=', $chapter->id)
                            ->exists();
                        if ($exists) {
                            $fail('Ce numéro de chapitre est déjà utilisé.');
                        }
                    },
                ],
                'existing_pages.*.page_number' => 'required|integer|min:1',
                'existing_pages.*.caption' => 'nullable|string|max:255',
                'existing_pages.*.new_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'new_pages.*.image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'new_pages.*.caption' => 'nullable|string|max:255',
                'new_pages.*.page_number' => 'required|integer|min:1',
                'pages_to_delete' => 'nullable|array',
                'pages_to_delete.*' => 'exists:comic_chapter_pages,id',
            ]);
    
            Log::info('Validation passed');
    
            DB::beginTransaction();
    
            // Mettre à jour les informations du chapitre
            $chapter->title = $request->title;
            $chapter->chapter_number = $request->chapter_number;
            $chapter->save();
    
            Log::info('Chapter updated', ['chapter_id' => $chapter->id]);
    
            // Supprimer les pages marquées pour suppression
            if ($request->has('pages_to_delete')) {
                foreach ($request->pages_to_delete as $pageId) {
                    $page = ComicChapterPage::find($pageId);
                    if ($page && $page->chapter_id == $chapter->id) {
                        // Supprimer l'image du stockage
                        if ($page->image_url) {
                            $path = str_replace('/storage/', 'public/', $page->image_url);
                            Storage::delete($path);
                        }
                        $page->delete();
                        Log::info('Page deleted', ['page_id' => $pageId]);
                    }
                }
            }
    
            // Mettre à jour les pages existantes
            if ($request->has('existing_pages')) {
                foreach ($request->existing_pages as $pageId => $pageData) {
                    $page = ComicChapterPage::find($pageId);
                    if ($page && $page->chapter_id == $chapter->id) {
                        $page->page_number = $pageData['page_number'];
                        $page->caption = $pageData['caption'] ?? null;
    
                        // Traiter la nouvelle image si elle existe
                        if (isset($pageData['new_image']) && $pageData['new_image']) {
                            $image = $pageData['new_image'];
                            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                            // Change the storage path to public instead of private
                            $imagePath = $image->storeAs('comics/chapters/' . $chapter->id, $imageName, 'public');
                            
                            // Supprimer l'ancienne image
                            if ($page->image_url) {
                                $oldPath = str_replace('/storage/', 'public/', $page->image_url);
                                Storage::delete($oldPath);
                            }
                            
                            $page->image_url = Storage::url($imagePath);
                        }
    
                        $page->save();
                        Log::info('Page updated', ['page_id' => $pageId]);
                    }
                }
            }
    
            // Ajouter les nouvelles pages
            if ($request->has('new_pages')) {
                foreach ($request->new_pages as $pageData) {
                    if (isset($pageData['image'])) {
                        // When creating a new chapter with pages
                        $image = $pageData['image'];
                        $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                        // Change the storage path to public instead of private
                        $imagePath = $image->storeAs('comics/chapters/' . $chapter->id, $imageName, 'public');
                        $imageUrl = '/storage/comics/chapters/' . $chapter->id . '/' . $imageName;
    
                        ComicChapterPage::create([
                            'chapter_id' => $chapter->id,
                            'page_number' => $pageData['page_number'],
                            'image_url' => $imageUrl,
                            'caption' => $pageData['caption'] ?? null
                        ]);
    
                        Log::info('New page created', [
                            'chapter_id' => $chapter->id,
                            'page_number' => $pageData['page_number']
                        ]);
                    }
                }
            }
    
            DB::commit();
    
            return redirect()
                ->route('authordashboard.comics.show', $chapter->comic)
                ->with('success', 'Chapitre mis à jour avec succès');
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating comic chapter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du chapitre: ' . $e->getMessage());
        }
    }
}
