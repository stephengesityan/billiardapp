<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Jika user sudah terverifikasi, redirect ke halaman utama
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath())
                ->with('success', 'Email anda sudah terverifikasi.');
        }
        
        // Jika user belum terverifikasi dan baru register (email_verified_at adalah null),
        // tampilkan halaman verifikasi
        return view('auth.verify');
    }

    /**
     * The user has been verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function verified(Request $request)
    {
        session()->flash('success', 'Email berhasil diverifikasi! Selamat datang di Ayo Venue.');
        return redirect($this->redirectPath());
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        // Jika user sudah terverifikasi, tidak perlu kirim ulang
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath())
                ->with('success', 'Email anda sudah terverifikasi.');
        }

        // Kirim email verifikasi baru
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email anda.');
    }
}