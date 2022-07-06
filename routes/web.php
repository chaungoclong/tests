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
    ->middleware(['check_permission', 'has_all_role:employee,manager,admin']);
Route::resource('test', \App\Http\Controllers\TestAbcController::class)
    ->middleware(['check_permission']);

Route::get('/', function () {
    auth()->logout();
    auth()->loginUsingId(2);
});
