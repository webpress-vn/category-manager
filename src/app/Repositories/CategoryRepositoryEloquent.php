<?php

namespace VCComponent\Laravel\Category\Repositories;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;

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

}
