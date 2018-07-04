<?php
use Faker\Generator as Faker;
use Modules\Notification\Models\Notification;

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

$factory->define(Notification::class, function (Faker $faker) {
    return [
        'type'           => null,
        'data'           => null,
        'read_at'           => $faker->dateTime,
    ];
});
