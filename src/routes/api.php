<?php

if (config('category.models.category')) {
    $model_class = config('category.models.category');
} else {
    $model_class = VCComponent\Laravel\Category\Entities\Category::class;
}

$model = new $model_class;
$api   = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => config('category.namespace')], function ($api) {
        $api->group(['prefix' => 'admin'], function ($api) {

            $api->get('categories/analytics', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController@analytics');

            $api->get('categories/all', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController@list');
            $api->put('categories/status/bulk', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController@bulkUpdateStatus');
            $api->put('categories/status/{id}', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController@updateStatusItem');
            $api->delete('categories/bulk-delete', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController@bulkDelete');
            $api->resource('categories', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\CategoryController');

            $api->get('category/tree', 'VCComponent\Laravel\Category\Http\Controllers\Api\Admin\getCategoriesTreeController@tree');
        });
        $api->get('categories/all', 'VCComponent\Laravel\Category\Http\Controllers\Api\Frontend\CategoryController@list');
        $api->put('categories/status/bulk', 'VCComponent\Laravel\Category\Http\Controllers\Api\Frontend\CategoryController@bulkUpdateStatus');
        $api->put('categories/status/{id}', 'VCComponent\Laravel\Category\Http\Controllers\Api\Frontend\CategoryController@updateStatusItem');
        $api->resource('categories', 'VCComponent\Laravel\Category\Http\Controllers\Api\Frontend\CategoryController');
    });
});
