<?php
use Faker\Generator as Faker;
use Modules\Post\Models\Post;

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

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title'           => $faker->unique(),
        'content'           => $faker->realText(),
        'password'           => $faker->password,
        'email'           => $faker->unique()->safeEmail,
        'category'           => $faker->randomElement(array (
  0 => '好人',
  1 => '坏蛋',
)),
        'status'           => null,
        'created_by'           => $faker->randomDigitNotNull,
    ];
});
