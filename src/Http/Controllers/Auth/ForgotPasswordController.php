<?php

namespace Porteiro\Http\Controllers\Auth;

use Auth;
use Porteiro;
use Former;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Porteiro\Models\Admin;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends ResetPasswordController
{
    

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        // Pass validation rules
        Former::withRules(
            [
            'email' => 'required|email',
            ]
        );

        // Set the breadcrumbs
        app('rica.breadcrumbs')->set(
            [
            route('facilitador.account@login') => 'Login',
            url()->current() => 'Forgot Password',
            ]
        );

        // Show the page
        $this->title = 'Forgot Password';
        $this->description = 'You know the drill.';

        return $this->populateView('facilitador::account.forgot');
    }
}
