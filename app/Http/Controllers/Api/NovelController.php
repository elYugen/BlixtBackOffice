<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Novel;
use Illuminate\Http\Request;

class NovelController extends Controller
{
    public function index()
    {
        $novels = Novel::with(['author.user', 'genres', 'tags'])->get();

        return response()->json($novels);
    }

    public function show(Novel $novel)
    {
        $novel->load(['author.user', 'genres', 'tags', 'chapters']);
        return response()->json($novel);
    }
}
