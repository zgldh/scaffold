<?php
/**
 * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field \App\Scaffold\Installer\Model\Field
 */
$factories = $MODEL->getFactories();
echo '<?php' ?>

use Faker\Generator as Faker;
use {{$moduleNamespace}}\Models\{{$MODEL->getModelName()}};

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

$factory->define({{$MODEL->getModelName()}}::class, function (Faker $faker) {
    return [
@foreach($factories as $name=>$factory)
        '{{$name}}'           => {!! $factory !!},
@endforeach
    ];
});
