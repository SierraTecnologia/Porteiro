<?php

/*
 * --------------------------------------------------------------------------
 * Activity Factory
 * --------------------------------------------------------------------------
*/

$factory->define(Porteiro\Models\Activity::class, function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'user_id' => 1,
        'description' => 'Standard User Activity',
        'request' => [],
    ];
});
