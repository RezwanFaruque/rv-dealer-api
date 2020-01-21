<?php
use Faker\Generator as Faker;
$factory->define(App\Rv::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'title' => $faker->sentence(),
        'brand_id' => $faker->randomNumber(),
        'model_id' => $faker->randomNumber(),
        'type_id' => $faker->randomNumber(),
        'condition' => $faker->boolean(),
        'stock_number' => str_random(12),
        'year' => $faker->year(),
        'price' => $faker->randomFloat(8000),
        'price_field' => $faker->randomFloat(8000),
        'monthly_payment' => $faker->randomFloat(),
        'is_special' => $faker->boolean(),
        'is_sold' => $faker->boolean(),
        'is_consignment' => $faker->boolean(),
        'is_new_arrival' => $faker->boolean(),
        'is_on_deposit' => $faker->boolean(),
        'is_on_order' =>  $faker->boolean(),
        'is_reduced' =>  $faker->boolean(),
        'is_active' => $faker->boolean(),
        'use_special_pricing' =>  $faker->boolean(),
        'use_click_to_call' => $faker->boolean(),
        'use_get_low_price' => $faker->boolean(),
        'hide_on_dealer_site' => $faker->boolean()
    ];
});
$factory->state(App\Rv::class, 'sold', [
    'is_sold' =>  true,
]);
$factory->state(App\Rv::class, 'not_sold', [
    'is_sold' =>  false,
]);