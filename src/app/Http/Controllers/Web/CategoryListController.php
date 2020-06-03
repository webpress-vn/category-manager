<?php

namespace VCComponent\Laravel\Category\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VCComponent\Laravel\Category\Contracts\ViewCategoryListControllerInterface;
use VCComponent\Laravel\Category\Pipes\ApplyConstraints;
use VCComponent\Laravel\Category\Pipes\ApplyOrderBy;
use VCComponent\Laravel\Category\Pipes\ApplySearch;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use VCComponent\Laravel\Category\Traits\Helpers;

class CategoryListController extends Controller implements ViewCategoryListControllerInterface
{
    protected $repository;
    protected $entity;
    use Helpers;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
    }

    public function index(Request $request)
    {
        if (method_exists($this, 'beforeQuery')) {
            $this->beforeQuery($request);
        }
        $pipes      = $this->pipes();
        $categories = $this->repository->getWithPagination($pipes);

        if (method_exists($this, 'afterQuery')) {
            $this->afterQuery($categories, $request);
        }

        $data = [
            // 'view_model' => $view_model,
            'categories' => $categories,
        ];
        $custom_view_data = $this->viewData($categories, $request);
        $data             = array_merge($custom_view_data, $data);
        if (method_exists($this, 'beforeView')) {
            $this->beforeView($data, $request);
        }
        return view($this->view(), $data);

    }

    protected function pipes()
    {
        return [
            ApplyConstraints::class,
            ApplySearch::class,
            ApplyOrderBy::class,
        ];
    }

    protected function view()
    {
        return 'category-manager::category-list';
    }

    protected function viewData($categories, Request $request)
    {
        return [];
    }
}
