<?php

namespace VCComponent\Laravel\Category\Test\Stubs\Models;

use VCComponent\Laravel\Category\Entities\Category as BaseCategoory;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Post\Entities\Post;

class Category extends BaseCategoory
{
    protected $table = 'categories';

    public function products()
    {
        return $this->morphedByMany(Product::class, 'categoryable');
    }
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'categoryable')->where('status',1)->orderBy('order', 'asc')->orderBy('id', 'desc');
    }
}
