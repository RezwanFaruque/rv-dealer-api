<?php
use Faker\Generator as Faker;
$factory->define(App\Brand::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'name' => $faker->sentence(),
    ];
});
