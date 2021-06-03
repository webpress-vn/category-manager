<?php

namespace VCComponent\Laravel\Category\Categories;
use Illuminate\Support\Facades\DB;
trait CategoryQueryTrait
{
    public function withRelationPaginateQuery($column, $value, $relations, $perPage)
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

    public function publishedQuery($value, $relation, $value_relation)
    {
        if ($relation !== null) {
            return $this->entity->select('name', 'slug', 'id')->where('status', self::STATUS_ACTIVE)->with($relation)->limit($value)->get()->map(function ($query) use ($value_relation, $relation) {
                $query->setRelation($relation, $query->$relation->take($value_relation));
                return $query;
            });
        }

        return $this->entity->where('status', self::STATUS_ACTIVE)->limit($value)->get();
    }
    public function publishedQuery($value, $relation, $value_relation)
    {
        if ($relation !== null) {
            return $this->entity->select('name', 'slug', 'id')->where('status', self::STATUS_ACTIVE)->with($relation)->limit($value)->get()->map(function ($query) use ($value_relation, $relation) {
                $query->setRelation($relation, $query->$relation->take($value_relation));
                return $query;
            });
        }

        return $this->entity->where('status', self::STATUS_ACTIVE)->limit($value)->get();
    }
}
