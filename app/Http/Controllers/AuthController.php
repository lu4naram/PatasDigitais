<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AuthController extends Controller
{
    // Mostrar formulário de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Processar login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Redirecionar baseado no role
            if ($user->isAdmin()) {
                return redirect()->intended('/animais');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas não são válidas.',
        ])->onlyInput('email');
    }

    // Mostrar formulário de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Processar registro
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'CLI', 
            ]);

            Auth::login($user);

            return redirect('/')->with('success', 'Cadastro realizado com sucesso!');
            
        } catch (Exception $e) {
            Log::error('Erro no cadastro: ' . $e->getMessage());
            return back()->with('error', 'Erro ao realizar cadastro.');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
