<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Hapus middleware verified karena kita akan mengizinkan user yang belum terverifikasi
        // untuk mengakses pengaturan akun mereka
        $this->middleware(['auth']);
    }

    /**
     * Show the account settings page.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $user = Auth::user();
        return view('account.settings', compact('user'));
    }

    /**
     * Update the user's account information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if current password is provided and is correct
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak valid.']);
            }
        }

        // Update user information
        $user->name = $validated['name'];
        
        // Check if email is changed
        $emailChanged = $user->email !== $validated['email'];
        if ($emailChanged) {
            $user->email = $validated['email'];
            
            // Set email_verified_at ke null hanya jika sebelumnya sudah terverifikasi
            // Ini untuk memastikan user harus verifikasi email baru
            if ($user->hasVerifiedEmail()) {
                $user->email_verified_at = null;
                $emailNeedsVerification = true;
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Jika email diubah dan sebelumnya sudah terverifikasi, kirim email verifikasi baru
        if ($emailChanged && isset($emailNeedsVerification)) {
            $user->sendEmailVerificationNotification();
            session()->flash('success', 'Profil berhasil diperbarui. Silakan verifikasi alamat email baru Anda.');
        } else {
            session()->flash('success', 'Profil berhasil diperbarui.');
        }

        return back();
    }
}