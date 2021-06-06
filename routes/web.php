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
  Route::get('/', 'UserController@welcome')->name('login');
  Route::get('tasks/download/excel', 'TaskController@exportExcel');
  Route::get('tasks/download/csv', 'TaskController@exportCsv');
  Route::get('tasks/download/json', 'TaskController@exportJson');

  Route::get('tasks/{parent}/deleted', 'TaskController@viewDeleted');
  Route::put('tasks/reorder', 'TaskController@swapOrder');
  
  Route::put('tasks/{id}/restore', 'TaskController@restore');
  Route::resource('tasks', TaskController::class);
});
Route::middleware(['guest'])->group(function () {
  Route::get('/login', 'UserController@index')->name('login');
  Route::get('/register', 'UserController@register');
});


