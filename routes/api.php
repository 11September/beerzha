<?php

Route::middleware('token')->get('beers', 'BeersController@index')->name('All Beers');
Route::middleware('token')->get('posts', 'PostsController@index')->name('All Posts');
Route::middleware('token')->get('dishes', 'DishesController@index')->name('Dishes in category');
Route::middleware('token')->get('types', 'TypesController@index')->name('Categories menu');
Route::middleware('token')->get('categories', 'TypesController@categories')->name('Categories bar');
Route::middleware('token')->get('orders', 'OrdersController@index')->name('Shoping cart');

// Bonuses routes
Route::middleware('token')->get('/bonuses/generate', 'BonusesController@generate_token')->name('generate_token');
Route::middleware('token')->get('/bonuses/check', 'BonusesController@check_tokens')->name('user tokens check');
Route::middleware('token')->get('bonuses', 'BonusesController@index')->name('Bonuses categories menu');
Route::middleware('token')->get('bonuses-dishes', 'BonusesController@dishes')->name('Dishes in bonuses category');
// Bonuses routes

// Notifications routes
Route::middleware('token')->get('notifications', 'NotificationsController@index')->name('All Notifications');
Route::middleware('token')->get('notification/feedback', 'NotificationsController@feedback')->name('Notification feedback');
// Notifications routes

// Orders routes
Route::middleware('token')->post('order', 'OrdersController@store')->name('Order Dish');
Route::middleware('token')->post('orders', 'OrdersController@order')->name('Order Dishes');
Route::middleware('token')->post('bonuses/orders', 'OrdersController@order_bonuses')->name('Order bonuses Dishes');
// Orders routes

// Sidebar menus
Route::middleware('token')->post('preOrder', 'UsersController@preOrder')->name('preOrder table');
Route::middleware('token')->get('history-orders', 'UsersController@history_orders')->name('history_orders');
Route::middleware('token')->get('menu', 'UsersController@menu')->name('history_orders');
// Sidebar menus

// Users routes
Route::post('registration', 'UsersController@register')->name('register');
Route::post('oauth', 'UsersController@login')->name('register');
Route::post('restore_password', 'UsersController@ResetPassword')->name('ResetPassword');
Route::post('social', 'UsersController@social')->name('social');
Route::middleware('token')->post('logout', 'UsersController@logout')->name('logout');
Route::middleware('token')->post('add_phone', 'UsersController@changePersonalPhone')->name('Change Personal Phone');
Route::middleware('token')->post('profile/edit', 'UsersController@changePersonalInfo')->name('Change Personal Info');
Route::middleware('token')->get('profile', 'UsersController@profile')->name('profile');
// Users routes

// Additional routes
Route::middleware('token')->post('code', 'OrdersController@code')->name('Day code and count Tables');
Route::middleware('token')->post('beerga', 'BeersController@store')->name('Order Beer');
Route::middleware('token')->get('delivery', 'UsersController@delivery')->name('delivery_possible');
// Additional routes