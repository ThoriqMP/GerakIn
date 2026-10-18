<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (!Auth::user()->hasCompletedOnboarding()) {
                return redirect()->route('onboarding');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => Str::lower($validated['username']),
            'email' => Str::lower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        // Create initial default profile
        UserProfile::create([
            'user_id' => $user->id,
            'gender' => 'male',
            'age' => 25,
            'height_cm' => 170,
            'current_weight_kg' => 70,
            'target_weight_kg' => 68,
            'fitness_goal' => 'hypertrophy',
            'weekly_target_days' => 4,
            'preferred_sports' => ['gym'],
            'preferred_unit' => 'kg',
            'default_rest_seconds' => 90,
            'theme' => 'dark',
            'accent_badge' => 'lightning',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('onboarding');
    }

    /**
     * One-click demo login as Budi Prakoso (The Gym Regular)
     */
    public function quickDemoLogin(Request $request)
    {
        $budi = User::where('email', 'budi@gerakin.app')->first();

        if (!$budi) {
            // Re-seed if missing
            \Artisan::call('db:seed');
            $budi = User::where('email', 'budi@gerakin.app')->first();
        }

        Auth::login($budi, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat datang kembali di GerakIn, Budi!');
    }

    /**
     * One-click create fresh test user to test the 3-step onboarding flow
     */
    public function freshDemoUser(Request $request)
    {
        $uniq = Str::random(5);
        $user = User::create([
            'name' => 'User Baru ' . $uniq,
            'username' => 'user_' . $uniq,
            'email' => 'user_' . $uniq . '@test.app',
            'password' => Hash::make('secret123'),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'gender' => 'female',
            'age' => 27,
            'height_cm' => 165,
            'current_weight_kg' => 65,
            'target_weight_kg' => 58,
            'fitness_goal' => 'lose_fat',
            'weekly_target_days' => 3,
            'preferred_sports' => ['running'],
            'preferred_unit' => 'kg',
            'default_rest_seconds' => 60,
            'theme' => 'dark',
            'accent_badge' => 'lightning',
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('onboarding')->with('info', 'Silakan selesaikan 3 langkah kustomisasi akun Anda!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari aplikasi.');
    }
}
