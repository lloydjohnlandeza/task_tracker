<?php
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
Route::middleware(['auth'])->group(function () {
  Route::get('/', function() {
    return Auth::user();
  });
  Route::get('tasks/download', 'TaskController@export');
  Route::resource('tasks', TaskController::class);
});
Route::middleware(['guest'])->group(function () {
  Route::get('/login', 'UserController@index')->name('login');
  Route::get('/register', 'UserController@register');
});


