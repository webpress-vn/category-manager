<?php

namespace VCComponent\Laravel\Category\Categories;

use Illuminate\Support\Facades\Cache;
use VCComponent\Laravel\Category\Categories\CategoryQueryTrait;
use VCComponent\Laravel\Category\Entities\Category as Entity;

class Category
{
    use CategoryQueryTrait;

    public $entity;
    const STATUS_PENDING = 2;
    const STATUS_ACTIVE  = 1;

    protected $cache        = false;
    protected $cacheMinutes = 60;

    public function __construct()
    {
        if (isset(config('category.models')['category'])) {
            $model        = config('category.models.category');
            $this->entity = new $model;
        } else {
            $this->entity = new Entity;
        }

        if (isset(config('category.cache')['enabled']) === true) {
            $this->cache     = true;
            $this->timeCache = config('category.cache')['minutes'] ? config('category.cache')['minutes'] * 60 : $this->cacheMinutes * 60;
        }
    }

    public function withRelationPaginate($column = '', $value = '', $relations = 'products', $perPage = 5)
    {
        if ($this->cache === true) {
            if (Cache::has('withRelationPaginate') && Cache::get('withRelationPaginate')->count() !== 0) {
                return Cache::get('withRelationPaginate');
            }
            return Cache::remember('withRelationPaginate', $this->timeCache, function () use ($column, $value, $relations, $perPage) {
                return $this->withRelationPaginateQuery($column, $value, $relations, $perPage);
            });
        }
        return $this->withRelationPaginateQuery($column, $value, $relations, $perPage);
    }

    public function published($value = 4, $relation = null, $value_relation = 5)
    {
        if ($this->cache === true) {
            if (Cache::has('published') && Cache::get('published')->count() !== 0) {
                return Cache::get('published');
            }
            return Cache::remember('published', $this->timeCache, function () use ($value, $relation, $value_relation) {
                return $this->publishedQuery($value, $relation, $value_relation);
            });
        }
        return $this->publishedQuery($value, $relation, $value_relation);
    }
}
