<?php

namespace VCComponent\Laravel\Category\Categories;

use VCComponent\Laravel\Category\Categories\Contracts\Category as ContractsCategory;
use VCComponent\Laravel\Category\Entities\Category as EntitiesCategory;

class Category implements ContractsCategory
{

    public $entity;
    protected $limit;
    protected $column;
    protected $value;
    protected $id;
    protected $attributes = [];
    protected $direction;
    protected $relations;
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE  = 2;

    public function __construct()
    {
        if (isset(config('category.models')['category'])) {
            $model        = config('category.models.category');
            $this->entity = new $model;
        } else {
            $this->entity = new EntitiesCategory;
        }
    }

    public function withRelationPaginate($column = '', $value = '', $relations = 'products', $perPage = 5)
    {

        switch ($relations) {
            case "posts":
                $post = $this->entity->where($column, $value)->first();
                if ($post) {
                    return $post->posts()->paginate($perPage);
                }
                break;
            case "products":
                $product = $this->entity->where($column, $value)->first();
                if ($product) {
                    return $product->products()->paginate($perPage);
                }
                break;
            default:
                return $this;
                break;
        }
    }

    public function where($column, $value)
    {
        $query = $this->entity->where($column, $value)->get();

        return $query;
    }

    public function findOrFail($id)
    {
        return $this->entity->findOrFail($id);
    }

    public function toSql()
    {
        return $this->entity->toSql();
    }

    public function get()
    {
        return $this->entity->get();
    }
    public function published()
    {
        $query = $this->entity->where('status', self::STATUS_ACTIVE)->limit(4)->get();

        return $query;
    }

    public function paginate($perPage)
    {
        return $this->entity->paginate($perPage);
    }

    public function limit($value)
    {

        return $this->entity->limit($value);
    }

    public function orderBy($column, $direction = 'asc')
    {
        return $this->entity->orderBy($column, $direction);
    }

    public function with($relations)
    {
        $this->entity->with($relations);

        return $this;
    }

    public function first()
    {
        return $this->entity->first();
    }

    public function create(array $attributes = [])
    {
        return $this->entity->create($attributes);
    }

    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->entity->firstOrCreate($attributes, $values);
    }

    public function update(array $values)
    {
        return $this->entity->update($values);
    }

    public function delete()
    {
        return $this->entity->delete();
    }
}
