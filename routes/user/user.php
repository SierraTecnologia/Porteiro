<?php

use Illuminate\Support\Str;
use Porteiro\Facades\Porteiro;

// Route::group(['prefix' => 'facilitador'], function () {
//     Porteiro::routes();
// });


Route::post('pusher/auth', function () {
    return auth()->user();
});
/*
|--------------------------------------------------------------------------
| Porteiro Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Porteiro.
|
*/
            

// Route::group(
//     ['as' => 'profile.'], function () {

        // Route::namespace('User')->group(
        //     function () {
                // Route::group(
                //     ['middleware' => 'admin.user'], function () {

                        // Main Admin and Logout Route
                        Route::get('/', ['uses' => 'PorteiroController@index',   'as' => 'dashboard']);
                        Route::post('logout', ['uses' => 'PorteiroController@logout',  'as' => 'logout']);
                        Route::post('upload', ['uses' => 'PorteiroController@upload',  'as' => 'upload']);

                        Route::get('profile', ['uses' => 'PorteiroUserController@profile', 'as' => 'profile']);

                        Route::get('/', 'ProfileController@index')->name('home');
                        Route::get('/show', 'ProfileController@show')->name('profile.show');
                        
                        Route::get('settings', 'SettingsController@settings');
                        Route::post('settings', 'SettingsController@update');
                        Route::get('password', 'PasswordController@password');
                        Route::post('password', 'PasswordController@update');

                    // }
                // );
            // }
    //     );
    // }
// );


// Route::group(['prefix' => 'user', 'middleware' => 'auth:user'], function()
// {
//     $a = 'user.';
//     Route::get('/', ['as' => $a . 'home', 'uses' => 'UserController@getHome']);

//     Route::group(['middleware' => 'activated'], function ()
//     {
//         $m = 'activated.';
//         Route::get('protected', ['as' => $m . 'protected', 'uses' => 'UserController@getProtected']);
//     });

// });





// /*
// |--------------------------------------------------------------------------
// | User Routes
// |--------------------------------------------------------------------------
// */
// Route::group(['middleware' => 'user', 'prefix' => 'user', 'as'=>'user.', 'namespace' => 'User'], function () {
 
//     Route::group(['prefix' => 'notifications'], function () {
//         Route::get('/', 'NotificationController@index');
//         Route::get('{uuid}/read', 'NotificationController@read');
//         Route::delete('{uuid}/delete', 'NotificationController@delete');
//         Route::get('search', 'NotificationController@search');
//     });
    
//     Route::get('settings', 'SettingsController@settings');
//     Route::post('settings', 'SettingsController@update');
//     Route::get('password', 'PasswordController@password');
//     Route::post('password', 'PasswordController@update');
// });
