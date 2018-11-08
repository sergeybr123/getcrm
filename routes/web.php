<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // Admin
    Route::prefix('admin')->name('admin.')->group(function() {
        Route::resource( 'plans', 'Admin\PlansController');
    });
    // Manager
    Route::prefix('manager')->name('manager.')->group(function() {
        Route::resource( 'users', 'Manager\UsersController');
        Route::post('users', 'Manager\UsersController@index')->name('user.search');
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
