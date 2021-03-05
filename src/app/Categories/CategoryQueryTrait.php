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
    public function getCategoriesQuery($post_type, $number, $pagination, $order_by, $order, $is_hot, $status) {
        $query =  $this->entity->where('type', $post_type)
            ->with('languages')
            ->orderBy($order_by,$order)
            ->where('is_hot',$is_hot)
            ->where('status', $status);
        if ($pagination === true) {
            return $query->paginate($number);
        }
        return  $query->limit($number)->get();
    }
    public function getPostCategoriesQuery($post_id, $post_type, $number, $pagination, $order_by, $order, $is_hot, $status) {
        $query = DB::table('categoryables')->where('categoryable_id',$post_id)
        ->where('categoryable_type',$post_type)
        ->join('categories', 'category_id', '=', 'categories.id')->select('categories.*')
            ->orderBy($order_by,$order)
            ->where('is_hot',$is_hot)
            ->where('status', $status);
        if ($pagination === true) {
            return $query->paginate($number);
        }
        return  $query->limit($number)->get();
    }

}
