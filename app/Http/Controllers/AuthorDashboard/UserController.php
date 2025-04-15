<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Author;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $author = Auth::user()->author;
        return view('authordashboard.author.index', compact('author'));
    }

    public function update(Request $request)
    {
        $author = Auth::user()->author;

        $request->validate([
            'pen_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:6048',
        ]);

        $author->pen_name = $request->pen_name;
        $author->bio = $request->bio;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($author->avatar) {
                Storage::disk('public')->delete($author->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $author->avatar = $avatarPath;
        }

        $author->save();

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }
}
