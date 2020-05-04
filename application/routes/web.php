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

// Route::get('/', function () {
//     return view('welcome');
// });

/**
 * User Route
 */

//Route::post('hash','UserController@hash');

Route::post('user/login','UserController@login');

Route::middleware('auth')->post('user/reset_password','UserController@reset_password');
Route::post('user/forget_password','UserController@forget_password');
Route::middleware('admin')->post('user/reset_password_all','UserController@reset_password_all');

Route::middleware('admin')->get('cache','UserController@clear_cache');
Route::middleware('auth')->post('user/logout','UserController@logout');
Route::middleware('auth')->get('user','UserController@get_current');
Route::middleware('admin')->get('users','UserController@get_all');
Route::middleware('auth')->post('user/{id}','UserController@edit');
Route::middleware('admin')->delete('user/{id}','UserController@delete');
Route::middleware('admin')->post('user','UserController@create');


/**
 * Event Route
 */

Route::middleware('admin')->post('event','EventController@create');
Route::middleware('auth')->get('event','EventController@get');
Route::middleware('admin')->post('event/{id}','EventController@edit');
Route::middleware('admin')->delete('event/{id}','EventController@delete');
Route::middleware('admin')->post('event/restore/{id}','EventController@restore');

/**
 * Period Route
 */

Route::middleware('admin')->post('period','PeriodController@create');
Route::middleware('auth')->get('period','PeriodController@get');
Route::middleware('admin')->delete('period/{id}','PeriodController@delete');

/**
 * Day Route
 */

Route::middleware('auth')->get('day','DayController@get');
Route::middleware('admin')->post('day/{id}','DayController@edit');

/**
 * Class Route
 */

Route::middleware('admin')->post('class','_ClassController@create');
Route::middleware('auth')->get('class','_ClassController@get');
Route::middleware('admin')->post('class/{id}','_ClassController@edit');
Route::middleware('admin')->delete('class/{id}','_ClassController@delete');

/**
 * Subject Route
 */

Route::middleware('admin')->post('subject','SubjectController@create');
Route::middleware('auth')->get('subject','SubjectController@get');
Route::middleware('admin')->post('subject/{id}','SubjectController@edit');
Route::middleware('admin')->delete('subject/{id}','SubjectController@delete');

/**
 * Choice Route
 */

Route::middleware('admin')->get('choice','ChoiceController@get_all');
Route::middleware('auth')->post('choice/submit','ChoiceController@submit');
Route::middleware('auth')->get('choice/user','ChoiceController@get_related');
Route::middleware('admin')->get('choice/user/{id}','ChoiceController@get_related_id');
Route::middleware('auth')->post('choice/{id}','ChoiceController@edit');
Route::middleware('auth')->delete('choice/{id}','ChoiceController@delete');

/**
 * Class User Route
 */
Route::middleware('admin')->get('class_user','Class_userController@get');
Route::middleware('admin')->post('class_user','Class_userController@create');
Route::middleware('admin')->delete('class_user/{id}','Class_userController@delete');

Route::middleware('admin')->get('export/{id}', 'ExportController@export')->name('export');

