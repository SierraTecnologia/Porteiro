<?php

        

                
Route::resource('users', 'UserController');
Route::resource('permissions', 'PermissionController');
Route::resource('roles', 'RoleController');

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/
Route::resource('users', 'UserController', ['except' => ['create', 'show']]);
Route::post('users/search', 'UserController@search'); //->name('users.search');
Route::get('users/search', 'UserController@index')->name('users.search');
Route::get('users/invite', 'UserController@getInvite')->name('users.invite');
Route::get('users/switch/{id}', 'UserController@switchToUser')->name('users.switch');
Route::post('users/invite', 'UserController@postInvite')->name('users.invite');

Route::get('/users/switch-back', 'UserController@switchUserBack')->name('users.back');

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/
Route::resource('roles', 'RoleController', ['except' => ['show']]);
Route::post('roles/search', 'RoleController@search');
Route::get('roles/search', 'RoleController@index')->name('roles.search');
