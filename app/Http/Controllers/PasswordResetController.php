<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']]);

        // Akun nonaktif tidak boleh memulihkan akses sendiri.
        $credentials = [
            'email' => Str::lower(trim($request->input('email'))),
            'is_active' => true,
        ];

        try {
            $status = Password::sendResetLink($credentials);
        } catch (Throwable $e) {
            Log::error('Pengiriman tautan reset kata sandi gagal: '.$e->getMessage());

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email gagal dikirim. Periksa pengaturan email aplikasi atau minta Project Manager mengubah kata sandi Anda.']);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('passwords.throttled')]);
        }

        // Jawaban sama untuk email terdaftar dan tidak terdaftar, supaya daftar akun tidak bisa ditebak.
        return back()->with('status', 'Jika email terdaftar dan akunnya aktif, tautan untuk membuat kata sandi baru sudah dikirim.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => Str::lower(trim($validated['email'])),
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $validated['token'],
                'is_active' => true,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('passwords.'.$status)]);
        }

        return redirect()->route('login')->with('status', 'Kata sandi sudah diperbarui. Silakan masuk dengan kata sandi baru.');
    }
}

