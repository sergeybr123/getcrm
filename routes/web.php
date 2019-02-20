<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');



Auth::routes();
Route::middleware('auth')->group(function() {

    Route::get('exchange-multilink', 'Admin\MultiLinkController@change_multilink')->name('change_multilink');

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // Admin
    Route::prefix('admin')->name('admin::')->group(function() {
        Route::prefix('plans')->name('plans::')->group(function() {
            Route::get( '', 'Admin\PlansController@index')->name('index');
            Route::match(['get', 'post'], '/create', 'Admin\PlansController@create')->name('create');
            Route::match(['get', 'post'], '/{id}/update', 'Admin\PlansController@update')->name('update');
            Route::post('/{id}/delete', 'Admin\PlansController@delete')->name('delete');
        });
        Route::prefix('subscribes')->name('subscribes::')->group(function(){
            Route::get('', 'Admin\SubscribeController@index')->name('index');
        });
    });
    // Manager
    Route::prefix('manager')->name('manager.')->group(function() {
        Route::prefix('mywork')->name('mywork.')->group(function(){
            Route::get( '', 'Manager\MyWorkController@index')->name('index');
        });
        // Работа с пользователями
        Route::prefix('users')->name('users.')->group(function() {
            Route::get('', 'Manager\UsersController@index')->name('index');
            Route::get('create', 'Manager\UsersController@create')->name('create');
            Route::post('store', 'Manager\UsersController@store')->name('store');
            Route::get('{id}', 'Manager\UsersController@show')->name('show');
            Route::get('{id}/edit', 'Manager\UsersController@edit')->name('edit');
            Route::post('{id}/update', 'Manager\UsersController@update')->name('update');
            Route::match(['get', 'post'], 'create/{id}/invoice', 'Manager\UsersController@invoice')->name('create_invoice');
            Route::post('/delete-chat/{id}/{user_id}', 'Manager\UsersController@delete_chat')->name('delete_chat');
        });
        Route::prefix('pages')->name('pages.')->group(function(){
            Route::get( '', 'Manager\PagesController@index')->name('index');
        });
        Route::prefix('bots')->name('bots.')->group(function(){
            Route::get( 'old', 'Manager\BotsController@bot_old')->name('bot_old');
            Route::get( 'new', 'Manager\BotsController@bot_new')->name('bot_new');
            Route::get('confirm', 'Manager\BotsController@confirm')->name('confirmBot');
            Route::get('reset', 'Manager\BotsController@reset')->name('resetBot');
        });

        // Работа со счетами
        Route::prefix('invoices')->name('invoices.')->group(function() {
            Route::get( '', 'Manager\InvoicesController@index')->name('index');
        });
        Route::prefix('subscribes')->name('subscribes.')->group(function(){
            Route::get('', 'Manager\SubscribesController@index')->name('index');
        });

        // Оплатить и активировать
        Route::post('/pay-activate', 'Manager\UsersController@payActivate')->name('pay.activate');
    });
    // Partner
    Route::prefix('partner')->name('partner::')->group(function(){
        Route::prefix('users')->name('users::')->group(function(){
            Route::get('', 'Partner\UserController@index')->name('index');
            Route::get('{id}/show', 'Partner\UserController@show')->name('show');
            Route::match(['get', 'post'], 'create-bot/{id?}', 'Partner\UserController@createBot')->name('create-bot');
        });
    });

    // Редактирование ссылки страниц и авточатов
    Route::post('edit-link', 'Manager\PagesController@editLink')->name('edit_link');
    // Изменение типа подписки пользователя для перехода на новый тарифный план
    Route::post('change-plan', 'Manager\UsersController@change_plan')->name('change_plan');


});
// Создание авточата
Route::post('create-bot', 'Manager\UsersController@createBot')->name('create_bot');





//Route::get('/home', 'HomeController@index')->name('home');

//Route::get('select', 'Manager\UsersController@selectNotSubscribed');

//Route::get('/inv', function () {
//    return view('manager.invoices.show');
//});
