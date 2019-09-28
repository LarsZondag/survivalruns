<?php

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

Route::get('/', function () {

    $date = \Carbon\Carbon::now();
    $year = $date->year;
    if ($date->month < 9) {
        $year--;
    }

    return redirect($year);
});

Route::middleware(['auth'])->group(function() {
    Route::get('/admin', 'AdminController@index')->name('admin');
    Route::post('/new_members', 'MemberController@replace_all');
});

Auth::routes();

Route::get('/{year}', 'RunsYearController@index')->middleware('updateRunInformation');