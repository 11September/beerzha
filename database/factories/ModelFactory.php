<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Type::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'image' => $faker->image(),
    ];
});

$factory->define(App\Dish::class, function (Faker\Generator $faker) {
    return [
        'type_id' => 1,
        'title' => $faker->title,
        'description' => $faker->title,
        'price' => 25,
        'body' => $faker->text(),
        'weight'=> $faker->randomDigit(),
        'status' => $faker->randomElement(['PUBLISHED' ,'DRAFT', 'PENDING']),
    ];
});


$factory->define(App\Beer::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'price_min' => $faker->randomDigit(),
        'price_max' => $faker->randomDigit(),
        'price_stable' => $faker->randomDigit(),
        'price_quotations' => $faker->randomDigit(),
        'status' => $faker->randomElement(['PUBLISHED' ,'DRAFT', 'PENDING']),
        'last_order' => Carbon::now(),
    ];
});

$factory->define(App\Order::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'dish_id' => 1,
        'seat' => 1,
        'amount' => 1,
        'price' => $faker->randomDigit(),
        'status' => $faker->randomElement(['OPEN' ,'CLOSED']),
    ];
});


