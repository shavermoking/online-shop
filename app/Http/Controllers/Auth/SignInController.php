<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordFormRequest;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;

class SignInController extends Controller
{
    public function page(): Factory|Application|View|RedirectResponse
    {

        return view('auth.index');
    }

    public function handle(SignInFormRequest $request): ?RedirectResponse
    {

        if (!auth()->attempt($request->validated())){
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.'
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'));
    }

    public function logOut(): RedirectResponse
    {
        auth()->logout();

        \request()->session()->invalidate();

        \request()->session()->regenerateToken();

        return redirect()
            ->intended(route('home'));
    }


}
