<?php

namespace VCComponent\Laravel\Category\Repositories;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;


/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace VCComponent\Laravel\Category\Repositories;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        if (isset(config('category.models')['category'])) {
            return config('category.models.category');
        } else {
            return Category::class;
        }
    }

    public function getEntity()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getWithPagination($filters)
    {
        $request = App::make(Request::class);
        $query   = $this->getEntity();

        $items = App::make(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->then(function ($content) use ($request) {
                $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 15;
                $categories = $content->paginate($per_page);
                return $categories;
            });

        return $items;
    }

    public function getCategoriesQuery(array $where, $number = 10, $order_by ='order', $order = 'asc',$columns = ['*']) {
        $query =  $this->getEntity()->where($where)
            ->orderBy($order_by,$order);
        if ($number > 0) {
            return  $query->limit($number)->get($columns);
        }
        return $query->get($columns);
    }

    public function getCategoriesQueryPaginate(array $where, $number = 10, $order_by ='order', $order = 'asc', $columns = ['*']) {
        $query =  $this->getEntity()->select($columns)
            ->where($where)
            ->orderBy($order_by,$order)
            ->paginate($number);
        return $query;
    }

    public function getPostCategoriesQuery($post_id, array $where, $post_type = 'posts', $number = 10, $order_by = 'order', $order = 'asc') {
        $query = DB::table('categoryables')->where('categoryable_id',$post_id)
        ->where('categoryable_type',$post_type)
        ->join('categories', 'category_id', '=', 'categories.id')->select("categories.*")
            ->orderBy($order_by,$order)
            ->where($where);
        if ($number > 0) {
            return  $query->limit($number)->get();
        }
        return $query->get();
    }

    public function getPostCategoriesQueryPaginate($post_id, array $where, $post_type = 'posts', $number = 10, $order_by = 'order', $order = 'asc') {
        $query = DB::table('categoryables')->where('categoryable_id',$post_id)
        ->where('categoryable_type',$post_type)
        ->join('categories', 'category_id', '=', 'categories.id')->select('categories.*')
            ->orderBy($order_by,$order)
            ->where($where)
            ->paginate($number);
        return $query;
    }

}
