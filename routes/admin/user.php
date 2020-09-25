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
Route::post('users/search', 'UserController@search');
Route::get('users/search', 'UserController@index');
Route::get('users/invite', 'UserController@getInvite');
Route::get('users/switch/{id}', 'UserController@switchToUser');
Route::post('users/invite', 'UserController@postInvite');

Route::get('/users/switch-back', 'UserController@switchUserBack');

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/
Route::resource('roles', 'RoleController', ['except' => ['show']]);
Route::post('roles/search', 'RoleController@search');
Route::get('roles/search', 'RoleController@index');
