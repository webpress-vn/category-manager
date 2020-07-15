<?php

namespace VCComponent\Laravel\Category\Categories\Facades;

use Illuminate\Support\Facades\Facade;

class Category extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moduleCategory.category';
    }
}
