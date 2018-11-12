<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // Admin
    Route::prefix('admin')->name('admin.')->group(function() {
        Route::resource( 'plans', 'Admin\PlansController');
    });
    // Manager
    Route::prefix('manager')->name('manager.')->group(function() {
//        Route::resource( 'users', 'Manager\UsersController');
        Route::get('users', 'Manager\UsersController@index')->name('users.index');
        Route::post('users', 'Manager\UsersController@index')->name('users.search');
        Route::get('users/create', 'Manager\UsersController@create')->name('users.create');
        Route::post('users/store', 'Manager\UsersController@store')->name('users.store');
        Route::get('users/{id}', 'Manager\UsersController@show')->name('users.show');
        Route::get('users/{id}/edit', 'Manager\UsersController@edit')->name('users.edit');
        Route::post('users/{id}/update', 'Manager\UsersController@update')->name('users.update');
        Route::resource( 'pages', 'Manager\PagesController');
//        Route::resource( 'bots', 'Manager\BotsController');
        Route::get( 'bots/old', 'Manager\BotsController@bot_old');
        Route::get( 'bots/new', 'Manager\BotsController@bot_new');
        Route::resource( 'invoices', 'Manager\InvoicesController');

        // Оплатить и активировать
        Route::post('/pay-activate', 'Manager\UsersController@payActivate')->name('pay.activate');
    });
    // Partner
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('select', 'Manager\UsersController@selectNotSubscribed');

Route::get('/inv', function () {
    return view('manager.invoices.show');
});
