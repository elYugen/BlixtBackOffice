<?php

namespace App\Http\Controllers\AuthorDashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check())
        {
           return redirect()->to('/author/dashboard/stats');
        }

        return view('authordashboard.index');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([ 
            'email' => ['required', 'email'], 
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->deleted) {
            return back()->withErrors([
                'email' => 'Ce compte a été désactivé',
            ]);
        }

        if ($user && $user->role !== "auteur") {
            return back()->withErrors([
                'email' => 'Vous n\'êtes pas un auteur autorisé',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/author/dashboard/stats');
        }

        return back()->withErrors([ 
            'email' => 'Adresse mail ou mot de passe incorrect',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('authordashboard.login');
    }  

}
