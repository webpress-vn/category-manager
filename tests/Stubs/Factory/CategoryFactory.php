<?php

use Faker\Generator as Faker;
use VCComponent\Laravel\Category\Entities\Category;


$factory->define(Category::class, function (Faker $faker) {
    return [
        'name'       => $faker->words(rand(4, 7), true),
        'description' => $faker->sentences(rand(4, 7), true),
        'type' => 'products',
        'status'      => 1
    ];
});




