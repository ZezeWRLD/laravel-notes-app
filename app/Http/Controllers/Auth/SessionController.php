<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    /**
     * Show the login form (Like tutorial: Route::get('/login', [SessionController::class, 'create']))
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle login form submission (Like tutorial: Route::post('/login', [SessionController::class, 'store']))
     */
    public function store(Request $request)
    {
        // Validate credentials (Exactly like tutorial)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt authentication (Exactly like tutorial)
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Throw validation error (Like tutorial)
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Regenerate session (Exactly like tutorial)
        $request->session()->regenerate();

        // Redirect to intended page (Like tutorial)
        return redirect()->intended('/notes');
    }

    /**
     * Handle logout (Like tutorial: Route::delete('/logout', [SessionController::class, 'destroy']))
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        // Invalidate session (Exactly like tutorial)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
