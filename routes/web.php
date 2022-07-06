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
    ->middleware('has_any_permission:index_user,create_user,test');
Route::resource('test', \App\Http\Controllers\TestAbcController::class)
    ->middleware(['has_any_role:employee,super_admin','has_any_permission:index_user,update_user']);

Route::get('/', function () {
    auth()->logout();
    auth()->loginUsingId(2);
});
