<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Novel;
use App\Models\NovelChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NovelChapterController extends Controller
{
    public function create(Request $request)
    {
        $novels = Novel::where('author_id', Auth::user()->author->id)
            ->where('deleted', 0)
            ->get();

        $selectedNovel = null;
        $usedNumbers = [];
        
        if ($request->has('novel_id')) {
            $selectedNovel = Novel::where('id', $request->novel_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();
                
            $usedNumbers = NovelChapter::where('novel_id', $selectedNovel->id)
                ->pluck('chapter_number')
                ->toArray();
        }

        return view('authordashboard.novels.chapter_create', compact('novels', 'selectedNovel', 'usedNumbers'));
    }

    public function store(Request $request)
    {        
        //Log::info('Starting chapter creation', ['request' => $request->all()]);

        try {
            $novel = Novel::where('id', $request->novel_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();

            //Log::info('Novel found', ['novel_id' => $novel->id]);

            $request->validate([
                'novel_id' => 'required|exists:novels,id',
                'title' => 'required|string|max:255',
                'chapter_number' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = NovelChapter::where('novel_id', $request->novel_id)
                            ->where('chapter_number', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Ce numéro de chapitre est déjà utilisé.');
                        }
                    },
                ],
                'content' => 'required|string',
            ]);

            //Log::info('Validation passed');

            $chapter = NovelChapter::create([
                'novel_id' => $request->novel_id,
                'title' => $request->title,
                'chapter_number' => $request->chapter_number,
                'content' => $request->content,
                'view_count' => 0,
                'like_count' => 0,
                'is_premium' => false,
                'deleted' => false
            ]);

            //Log::info('Chapter created', ['chapter_id' => $chapter->id]);

            return redirect()
                ->route('authordashboard.novels.show', $novel)
                ->with('success', 'Chapitre créé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du chapitre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du chapitre.');
        }
    }

    public function edit(NovelChapter $chapter)
    {
        if ($chapter->novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }

        $usedNumbers = NovelChapter::where('novel_id', $chapter->novel_id)
            ->where('id', '!=', $chapter->id)
            ->pluck('chapter_number')
            ->toArray();

        return view('authordashboard.novels.chapter_edit', compact('chapter', 'usedNumbers'));
    }

    public function update(Request $request, NovelChapter $chapter)
    {
        if ($chapter->novel->author_id !== Auth::user()->author->id) {
            abort(403, 'Action non autorisée.');
        }

        $usedNumbers = NovelChapter::where('novel_id', $chapter->novel_id)
            ->where('id', '!=', $chapter->id)
            ->pluck('chapter_number')
            ->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'chapter_number' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $chapter) {
                    $exists = NovelChapter::where('novel_id', $chapter->novel_id)
                        ->where('chapter_number', $value)
                        ->where('id', '!=', $chapter->id)
                        ->exists();
                    if ($exists) {
                        $fail('Ce numéro de chapitre est déjà utilisé.');
                    }
                },
            ],
            'content' => 'required|string',
        ]);

        $chapter->update([
            'title' => $request->title,
            'chapter_number' => $request->chapter_number,
            'content' => $request->content,
        ]);

        return redirect()
            ->route('authordashboard.novels.show', $chapter->novel)
            ->with('success', 'Chapitre mis à jour avec succès');
    }

    public function getUsedNumbers($novel_id)
    {
        try {
            $novel = Novel::where('id', $novel_id)
                ->where('author_id', Auth::user()->author->id)
                ->firstOrFail();

            $usedNumbers = NovelChapter::where('novel_id', $novel->id)
                ->orderBy('chapter_number')
                ->pluck('chapter_number')
                ->toArray();

            return response()->json($usedNumbers);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Roman non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Error getting used chapter numbers: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue'], 500);
        }
    }
}
