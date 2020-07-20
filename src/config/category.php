<?php

return [

    'namespace'       => env('CATEGORY_COMPONENT_NAMESPACE', ''),

    'models'          => [
        'category' => VCComponent\Laravel\Category\Entities\Category::class,
    ],

    'transformers'    => [
        'category' => VCComponent\Laravel\Category\Transformers\CategoryTransformer::class,
    ],

    'auth_middleware' => [
        'admin'    => [
            'middleware' => '',
            'except'     => [],
        ],
        'frontend' => [
            'middleware' => '',
            'except'     => [],
        ],
    ],
    'cache'           => [
        'enabled' => false,
        'minutes' => 5,
    ],
];
