<?php

use Faker\Generator as Faker;
use Modules\Setting\Models\Setting;

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

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'name'          => $faker->unique(),
        'value'         => $faker->realText(),
        'settable_id'   => null,
        'settable_type' => null,
    ];
});
