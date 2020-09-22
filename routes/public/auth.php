<?php

if (config('siravel.login', true)) {
    Route::post('pusher/auth', function() {
        return auth()->user();
    });

    Auth::routes(['login' => 'auth.login']);

    Route::group(['middleware' => 'auth:all'], function()
    {
        $a = 'authenticated.';
        Route::get('/logout', ['as' => $a . 'logout', 'uses' => 'Auth\LoginController@logout']);
        Route::get('/activate/{token}', ['as' => $a . 'activate', 'uses' => 'User\ActivateController@activate']);
        Route::get('/activate', ['as' => $a . 'activation-resend', 'uses' => 'User\ActivateController@resend']);
        Route::get('not-activated', ['as' => 'not-activated', 'uses' => function () {
            return view('errors.not-activated');
        }]);
    });

    // DIferente do Outro, testar os dois
    $s = 'social.';
    Route::get('/social/redirect/{provider}',   ['as' => $s . 'redirect',   'uses' => 'Auth\SocialController@getSocialRedirect']);
    Route::get('/social/handle/{provider}',     ['as' => $s . 'handle',     'uses' => 'Auth\SocialController@getSocialHandle']);


    /*
    |--------------------------------------------------------------------------
    | Social Routes
    |--------------------------------------------------------------------------
    */
    Route::get('auth/{provider}', 'Auth\SocialiteAuthController@redirectToProvider');
    Route::get('auth/{provider}/callback', 'Auth\SocialiteAuthController@handleProviderCallback');


    /*
    |--------------------------------------------------------------------------
    | Login/ Logout/ Password
    |--------------------------------------------------------------------------
    */
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    /*
    |--------------------------------------------------------------------------
    | Registration & Activation
    |--------------------------------------------------------------------------
    */
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    Route::get('activate/token/{token}', 'Auth\ActivateController@activate');
    Route::group(['middleware' => ['auth']], function () {
        Route::get('activate', 'Auth\ActivateController@showActivate');
        Route::get('activate/send-token', 'Auth\ActivateController@sendToken');
    });

    /*
    |--------------------------------------------------------------------------
    | Subscription
    |--------------------------------------------------------------------------
    */
    Route::get('subscription', 'Auth\SubscriptionController@index')->name('subscription');
    Route::post('subscription', 'Auth\SubscriptionController@subscription');

}