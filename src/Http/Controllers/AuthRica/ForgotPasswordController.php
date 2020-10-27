<?php

namespace Porteiro\Http\Controllers\AuthRica;

use Auth;
use Former;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Porteiro;
use Porteiro\Models\Admin;

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
            route('porteiro.account@login') => 'Login',
            url()->current() => 'Forgot Password',
            ]
        );

        // Show the page
        $this->title = 'Forgot Password';
        $this->description = 'You know the drill.';

        return $this->populateView('facilitador::account.forgot');
    }
}
