<?php

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

$factory->define(App\Models\Access\User\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];

});

//$factory->define(App\Models\Hopper\EventSession::class, function (Faker\Generator $faker) {
//    return [
//        'session_id' => $faker->name,
//        'checked_in' => $faker->email,
//        'speakers' => $faker->name,
//        'onsite_phone' => $faker->phoneNumber,
//        'approval_brand' => $faker->boolean(),
//        'approval_legal' => $faker->boolean(),
//        'approval_revrec' => $faker->boolean(),
//        'dates_rooms' => null,
//        'presentation_owner' => $faker->name,
//        'remember_token' => str_random(10),
//    ];
//});
