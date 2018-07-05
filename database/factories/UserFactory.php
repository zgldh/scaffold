<?php

use Faker\Generator as Faker;
use Modules\User\Models\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => 'secret',
        'gender'         => $faker->randomElement([User::GENDER_MALE, User::GENDER_FEMALE]),
        'mobile'         => $faker->phoneNumber,
        'is_active'      => $faker->boolean,
        'last_login_at'  => $faker->dateTimeThisDecade,
        'login_times'    => $faker->numberBetween(1, 100),
        'remember_token' => str_random(10),
    ];
});
