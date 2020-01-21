<?php
use Faker\Generator as Faker;
$factory->define(App\Type::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'name' => $faker->word()
    ];
});
