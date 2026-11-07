<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    /**
     * Show the customer login form.
     */
    public function showLoginForm()
    {
        return view('storefront.auth.login');
    }

    /**
     * Handle a customer login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_admin' => false,
        ], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'Signed in successfully.');
        }

        return back()->with('error', 'Invalid credentials')->withInput($request->only('email'));
    }

    /**
     * Log the customer out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
