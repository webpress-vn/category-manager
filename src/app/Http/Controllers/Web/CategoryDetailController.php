<?php

namespace VCComponent\Laravel\Category\Http\Controllers\Web;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VCComponent\Laravel\Category\Contracts\ViewCategoryDetailControllerInterface;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use VCComponent\Laravel\Category\Traits\Helpers;

class CategoryDetailController extends Controller implements ViewCategoryDetailControllerInterface
{
    protected $repository;
    protected $entity;
    use Helpers;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
    }

    public function show($slug, Request $request)
    {
        if (method_exists($this, 'beforeQuery')) {
            $this->beforeQuery($request);
        }

        $category = $this->entity->findBySlugOrFail($slug);

        if (method_exists($this, 'afterQuery')) {
            $this->afterQuery($category, $request);
        }

        $data = [
            'category' => $category,
        ];

        $custom_view_data    = [];
        $view_data_func_name = 'viewData' . ucwords($category->type);
        if (method_exists($this, $view_data_func_name)) {
            $custom_view_data = $this->$view_data_func_name($category, $request);
        } else {
            $custom_view_data = $this->viewData($category, $request);
        }
        $data = array_merge($custom_view_data, $data);

        if (method_exists($this, 'beforeView')) {
            $this->beforeView($data, $request);
        }

        $key = 'view' . ucwords($category->type);
        if (method_exists($this, $key)) {
            return view($this->$key(), $data);
        } else {
            return view($this->view(), $data);
        }
    }

    protected function view()
    {
        return 'category-manager::category-detail';
    }

    protected function viewData($category, Request $request)
    {
        return [];
    }
}
