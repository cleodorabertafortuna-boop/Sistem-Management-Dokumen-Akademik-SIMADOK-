<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // ---------- ADMIN ----------
    public function showAdminLogin()
    {
        return view('auth.login-admin');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    // ---------- GURU (login dengan Nama + NIP) ----------
    public function showGuruLogin()
    {
        return view('auth.login-guru');
    }

    public function guruLogin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nip' => 'required|string',
        ]);

        $guru = \App\Models\Guru::where('nip', $request->nip)
            ->where('nama', $request->nama)
            ->first();

        if ($guru) {
            Auth::guard('guru')->login($guru);
            $request->session()->regenerate();
            return redirect()->route('guru.dashboard');
        }

        return back()->withErrors(['nip' => 'Nama atau NIP tidak ditemukan.'])->onlyInput('nama');
    }

    // ---------- SISWA (login dengan Nama + NIS) ----------
    public function showSiswaLogin()
    {
        return view('auth.login-siswa');
    }

    public function siswaLogin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nis' => 'required|string',
        ]);

        $siswa = \App\Models\Siswa::where('nis', $request->nis)
            ->where('nama', $request->nama)
            ->first();

        if ($siswa) {
            Auth::guard('siswa')->login($siswa);
            $request->session()->regenerate();
            return redirect()->route('siswa.dashboard');
        }

        return back()->withErrors(['nis' => 'Nama atau NIS tidak ditemukan.'])->onlyInput('nama');
    }

    // ---------- LOGOUT (berlaku untuk semua guard) ----------
    public function logout(Request $request)
    {
        foreach (['web', 'guru', 'siswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
