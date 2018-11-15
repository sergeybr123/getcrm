<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // Admin
    Route::prefix('admin')->name('admin::')->group(function() {
        Route::prefix('plans')->name('plans::')->group(function() {
            Route::get( '', 'Admin\PlansController@index')->name('index');
        });
        Route::prefix('subscribes')->name('subscribes::')->group(function(){
            Route::get('', 'Admin\SubscribeController@index')->name('index');
        });
    });
    // Manager
    Route::prefix('manager')->name('manager.')->group(function() {
        // Работа с пользователями
        Route::name('users.')->group(function() {
            Route::get('users', 'Manager\UsersController@index')->name('index');
//            Route::post('users', 'Manager\UsersController@index')->name('search');
            Route::get('users/create', 'Manager\UsersController@create')->name('create');
            Route::post('users/store', 'Manager\UsersController@store')->name('store');
            Route::get('users/{id}', 'Manager\UsersController@show')->name('show');
            Route::get('users/{id}/edit', 'Manager\UsersController@edit')->name('edit');
            Route::post('users/{id}/update', 'Manager\UsersController@update')->name('update');
        });
        Route::prefix('pages')->name('pages.')->group(function(){
            Route::get( '', 'Manager\PagesController@index')->name('index');
        });

//        Route::resource( 'bots', 'Manager\BotsController');
        Route::get( 'bots/old', 'Manager\BotsController@bot_old');
        Route::get( 'bots/new', 'Manager\BotsController@bot_new');
        // Работа со счетами
        Route::name('invoices.')->group(function() {
            Route::get( 'invoices', 'Manager\InvoicesController@index')->name('index');
//            Route::post('invoices', 'Manager\InvoicesController@index')->name('search');
        });


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
