<?php

Route::get('/', 'BeergaController@welcome')->name('welcome');
Route::get('/beers', 'BeersController@index')->name('getBeers');
Route::get('/code',  'BeergaController@code')->name('getCode');
Route::get('/ticker', 'BeergaController@getRunningLine')->name('getTicker');

Route::get('/notification/{notification}', 'NotificationsController@notificationPage')->name('notificationPageWeb');

Route::get('/bonuses/{bonusToken}', 'BonusesController@get_bonuses')->name('page-get-bonuses');
Route::get('/bonuses/purchase/{bonusToken}', 'BonusesController@checkout')->name('page-purchase-bonuses');

Route::post('/getbonuses', 'BonusesController@spendBonuses')->name('getbonuses');
Route::post('/getOrders', 'BonusesController@obtainingBonuses')->name('obtainingBonuses');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});