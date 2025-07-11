<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::login($admin);
            return redirect()->route('admin.all-report');
        }

        return back()->withErrors([
            'username' => 'Login gagal. Cek kembali username atau password.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
