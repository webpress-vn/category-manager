<?php

namespace VCComponent\Laravel\Category\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use VCComponent\Laravel\Category\Events\CategoryCreatedByAdminEvent;
use VCComponent\Laravel\Category\Events\CategoryDeletedEvent;
use VCComponent\Laravel\Category\Events\CategoryUpdatedByAdminEvent;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use VCComponent\Laravel\Category\Transformers\CategoryTransformer;
use VCComponent\Laravel\Category\Validators\CategoryValidator;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

trait CategoryAdminMethods
{
    public function __construct(CategoryRepository $repository, CategoryValidator $validator, Request $request)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
        $this->validator  = $validator;

        if (config('category.auth_middleware')['admin']['middleware']) {
            $this->middleware(
                config('category.auth_middleware')['admin']['middleware'],
                ['except' => config('category.auth_middleware.admin.except')]
            );
        }

        if (isset(config('category.transformers')['category'])) {
            $this->transformer = config('category.transformers.category');
        } else {
            $this->transformer = CategoryTransformer::class;
        }
    }

    public function index(Request $request)
    {
        $query = $this->entity;

        // $query = $this->applyQueryScope($query, 'type', $this->type);
        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $query = $this->countProductAndPost($request, $query);

        if ($request->has('type')) {
            $query = $query->where('type', $request->get('type'));
        }
        if ($request->has('status')) {
            $query = $query->where('status', $request->get('status'));
        }

        $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $categories = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($categories, $transformer);
    }

    function list(Request $request)
    {
        $query = $this->entity;
        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        if ($request->has('type')) {
            $query = $query->where('type', $request->get('type'));
        }
        if ($request->has('status')) {
            $query = $query->where('status', $request->get('status'));
        }
        $query      = $this->countProductAndPost($request, $query);
        $categories = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->collection($categories, $transformer);
    }

    public function show(Request $request, $id)
    {
        $category = $this->repository->findWhere(['id' => $id])->first();
        if (!$category) {
            throw new Exception("Danh mục không tồn tại", 1);
        }

        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToShow($user, $id)) {
                throw new PermissionDeniedException();
            }
        }
        // dd($category);
        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($category, $transformer);
    }

    public function store(Request $request)
    {
        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToCreate($user)) {
                throw new PermissionDeniedException();
            }
        }
        $data         = $request->all();
        $data['slug'] = Str::slug($data['name']);
        $category     = $this->repository->findWhere(['name' => $data['name'], 'type' => $data['type']])->first();
        if ($category) {
            throw new \Exception("Tên danh mục không được để trùng nhau", 1);
        }
        $this->validator->isValid($data, 'RULE_ADMIN_CREATE');

        $category = $this->repository->create($data);
        $category->save();

        event(new CategoryCreatedByAdminEvent($category));

        return $this->response->item($category, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $category = $this->repository->findWhere(['id' => $id])->first();
        if (!$category) {
            throw new Exception("Danh mục không tồn tại", 1);
        }

        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $data         = $request->all();
        $data['slug'] = Str::slug($data['name']);
        $category     = $this->entity->where('id', '<>', $id)
            ->where('name', $data['name'])
            ->where('type', $data['type'])
            ->first();
        if ($category) {
            throw new \Exception("Tên danh mục không được để trùng nhau", 1);
        }
        $this->validator->isValid($data, 'RULE_ADMIN_UPDATE');

        $category = $this->repository->update($data, $id);

        if ($request->has('status')) {
            $category->status = $request->get('status');
            $category->save();
        }

        event(new CategoryUpdatedByAdminEvent($category));

        return $this->response->item($category, new $this->transformer);
    }

    public function destroy(Request $request, $id)
    {
        $category = $this->repository->findWhere(['id' => $id])->first();
        if (!$category) {
            throw new NotFoundException('Category');
        }

        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToDelete($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $this->repository->delete($id);

        event(new CategoryDeletedEvent());

        return $this->success();
    }

    public function bulkDelete(Request $request)
    {
        $ids      = $request->ids;
        $category = $this->entity::whereIn('id', $ids);

        $category->delete();
        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdate($user)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $request->all();

        $categories = $this->entity->whereIn('id', $data['item_ids'])
            ->get();

        if ($categories->count() == 0) {
            throw new NotFoundException(' entities');
        }

        $this->validator->isValid($request, 'BULK_UPDATE_STATUS');

        foreach ($categories as $category) {
            $category->status = $data['status'];
            $category->save();
        }

        return $this->success();
    }

    public function updateStatusItem(Request $request, $id)
    {
        if (config('category.auth_middleware')['admin']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }
        $data             = $request->all();

        $category = $this->repository->findWhere(['id' => $id])->first();
        if (!$category) {
            throw new NotFoundException(' entity');
        }

        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $category->status = $data['status'];
        $category->save();

        return $this->success();
    }

    public function countProductAndPost($request, $query)
    {
        if ($request->has('count')) {
            $query = $query->withCount('products')->withCount('posts');
        }
        return $query;
    }

    public function analytics(Request $request)
    {
        $pattern = '/^(\d+\,?)*$/';

        if (!preg_match($pattern, $request->ids)) {
            throw new Exception('The input id is incorrect');
        }

        $ids = explode(",", $request->ids);

        $query = $this->entity->whereIn('id', $ids)->withCount('products')->withCount('posts')->get();

        return $query;
    }
}
