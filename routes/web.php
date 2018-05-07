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

Route::get('/', 'BeergaController@welcome')->name('welcome');
Route::get('/beers', 'BeersController@index')->name('getBeers');
Route::get('/code',  'BeergaController@code')->name('getCode');
Route::get('/ticker', 'BeergaController@getRunningLine')->name('getTicker');

Route::get('/notification/{notification}', 'NotificationsController@notificationPage')->name('notificationPageWeb');

Route::get('/bonuses/{bonusToken}', 'BonusesController@checkout')->name('page-get-bonuses');
Route::get('/bonuses/purchase/{bonusToken}', 'BonusesController@purchase')->name('page-purchase-bonuses');
Route::post('/getbonuses', 'BonusesController@getbonuses')->name('getbonuses');
Route::post('/getOrders', 'BonusesController@getOrders')->name('getOrders');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});