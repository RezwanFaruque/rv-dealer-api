<?php
use Faker\Generator as Faker;
$factory->define(App\Classification::class, function (Faker $faker) {
    return [
        'slug' => $faker->unique()->words(1),
    ];
});
