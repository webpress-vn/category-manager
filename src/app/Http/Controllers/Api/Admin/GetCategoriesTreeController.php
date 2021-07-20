<?php

namespace VCComponent\Laravel\Category\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class GetCategoriesTreeController extends ApiController
{
    public function __construct(CategoryRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();

        if (config('category.auth_middleware')['admin']['middleware']) {
            $this->middleware(
                config('category.auth_middleware')['admin']['middleware'],
                ['except' => config('category.auth_middleware.admin.except')]
            );
        }
    }
    
    public function tree(Request $request) {
        $query = $this->entity;
        
        if ($request->has('type')) {
            $query = $query->where('type', $request->get('type'));
        }

        $categories = $query->where('parent_id', 0)->where('status', 1)->with('children')->get();

        $categories = $this->mapChildren($categories);

        return response()->json($categories);
    }

    protected function mapChildren($categories) {
        return $categories->map(function ($item) {
            $children = [];
            if ($item->children) {
                $children = $this->mapChildren($item->children);
            }
            return [
                'name' => $item->name,
                'children' => $children
            ];
        });
    }
}