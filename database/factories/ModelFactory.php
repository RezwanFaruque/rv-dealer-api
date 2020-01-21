<?php
use Faker\Generator as Faker;
$factory->define(App\RvModel::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'brand_id' => $faker->randomNumber(),
        'name' => $faker->sentence(),
    ];
});
