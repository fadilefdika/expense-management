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
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin) {
            return back()->withErrors([
                'username' => 'Username tidak ditemukan.',
            ])->withInput();
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->withInput();
        }

        Auth::login($admin);
        return redirect()->route('admin.all-report');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
