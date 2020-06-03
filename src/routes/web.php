<?php

Route::prefix(config('category.namespace'))
    ->middleware('web')
    ->group(function () {
        Route::get('categories', 'VCComponent\Laravel\Category\Contracts\ViewCategoryListControllerInterface@index');
        Route::get('/categories/{slug}', 'VCComponent\Laravel\Category\Contracts\ViewCategoryDetailControllerInterface@show');
    });
