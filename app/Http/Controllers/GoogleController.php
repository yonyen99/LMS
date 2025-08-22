<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     * This method initiates the OAuth flow by redirecting the user to Google's login page.
     * It requests access to the user's email and profile information.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function googlepage()
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     * This method handles the callback from Google after the user has authenticated.
     * It retrieves the user's information, checks if they already exist in the database,
     * and logs them in or creates a new user if they don't exist.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function googlecallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {
                Auth::login($finduser);
                return redirect()->intended('/home');
            }

            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => bcrypt('12345678'),
            ]);

            Auth::login($newUser);
            return redirect()->intended('/home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }
}