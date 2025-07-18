<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Pastikan middleware auth hanya untuk method yang membutuhkan autentikasi
        $this->middleware('auth')->except(['verify']);
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $user = \App\Models\User::find($request->route('id'));

        if (!$user) {
            return redirect()->route('home')
                ->with('error', 'User tidak ditemukan.');
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('home')
                ->with('error', 'Link verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            // Jika user sudah verifikasi sebelumnya, pastikan tidak login otomatis
            return redirect()->route('home')
                ->with('verified', 'Email sudah terverifikasi sebelumnya. Silakan login.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Pastikan user harus login setelah verifikasi
        if ($request->user()) {
            auth()->logout();
        }

        return redirect()->route('home')
            ->with('verified', 'Email berhasil diverifikasi. Silakan login.');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home')
                ->with('success', 'Email Anda sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true)
            ->with('success', 'Link verifikasi telah dikirim ulang ke alamat email Anda.');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        // Redirect ke homepage dengan pesan untuk verifikasi
        return redirect()->route('home')
            ->with('error', 'Silakan verifikasi email Anda terlebih dahulu. 
                Link verifikasi telah dikirim ke alamat email Anda.')
            ->with('resend_link', true);
    }
}