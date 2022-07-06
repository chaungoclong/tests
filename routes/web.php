<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('users', \App\Http\Controllers\UserController::class)
    ->middleware('check_permission');
Route::resource('test', \App\Http\Controllers\TestAbcController::class);

Route::get('/', function () {
    $user = User::first();
    $permission = \App\Models\Permission::first();
    $role = \App\Models\Role::first();

    dd(
        $user->getAllActions(),
        $user->hasRole(1)
    );
});
