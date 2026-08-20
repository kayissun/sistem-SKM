<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathForRole());
    }

    /**
     * Tentukan halaman tujuan setelah login berdasarkan role user.
     */
    protected function redirectPathForRole(): string
    {
        $user = Auth::user();

        return match (true) {
            $user->hasRole('dinkes') => route('dinkes.dashboard', absolute: false),
            $user->hasRole('dinkes-skm') => route('puskesmas.dashboard', absolute: false),
            $user->hasRole('admin-puskesmas') => route('puskesmas.dashboard', absolute: false),
            $user->hasRole('petugas') => route('puskesmas.laporan.index', absolute: false),
            default => route('dashboard', absolute: false),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
