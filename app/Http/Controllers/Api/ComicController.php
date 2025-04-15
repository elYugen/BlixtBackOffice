<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\Http\Request;

class ComicController extends Controller
{
    public function index()
    {
        $comics = Comic::with(['author.user', 'genres', 'tags'])->get();

        return response()->json($comics);
    }

    public function show(Comic $comic)
    {
        $comic->load(['author.user', 'genres', 'tags', 'chapters']);
        return response()->json($comic);
    }
}
