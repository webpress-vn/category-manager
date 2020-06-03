<?php

namespace VCComponent\Laravel\Category\Traits;

use Illuminate\Http\Request;
use VCComponent\Laravel\Category\Events\CategoryCreatedEvent;
use VCComponent\Laravel\Category\Events\CategoryDeletedEvent;
use VCComponent\Laravel\Category\Events\CategoryUpdatedEvent;
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
use VCComponent\Laravel\Category\Transformers\CategoryTransformer;
use VCComponent\Laravel\Category\Validators\CategoryValidator;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

trait CategoryFrontendMethods
{
    public function __construct(CategoryRepository $repository, CategoryValidator $validator, Request $request)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
        $this->validator  = $validator;

        if (config('category.auth_middleware')['frontend']['middleware']) {
            $this->middleware(
                config('category.auth_middleware')['frontend']['middleware'],
                ['except' => config('category.auth_middleware.frontend.except')]
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

        $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $categories = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($categories, $transformer);
    }

    function list(Request $request) {
        $query = $this->entity;

        // $query = $this->applyQueryScope($query, 'type', $this->type);
        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

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
        $category = $this->repository->findWhere(['id' => $id, 'type' => $this->type])->first();
        if (!$category) {
            throw new NotFoundException(title_case($this->type) . ' entity');
        }

        if (config('category.auth_middleware')['frontend']['middleware']) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToShow($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($category, $transformer);
    }

    public function store(Request $request)
    {
        if (config('category.auth_middleware')['frontend']['middleware'] !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToCreate($user)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $request->all();

        $this->validator->isValid($data, 'RULE_ADMIN_CREATE');

        $category = $this->repository->create($data);
        $category->save();

        event(new CategoryCreatedEvent($category));

        return $this->response->item($category, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $category = $this->repository->findWhere(['id' => $id, 'type' => $this->type])->first();
        if (!$category) {
            throw new NotFoundException(title_case($this->type) . ' entity');
        }

        if (config('category.auth_middleware')['frontend']['middleware'] !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $request->all();

        $this->validator->isValid($data, 'RULE_ADMIN_UPDATE');

        $category = $this->repository->update($data, $id);

        if ($request->has('status')) {
            $category->status = $request->get('status');
            $category->save();
        }

        event(new CategoryUpdatedEvent($category));

        return $this->response->item($category, new $this->transformer);
    }

    public function destroy(Request $request, $id)
    {
        $category = $this->repository->findWhere(['id' => $id])->first();
        if (!$category) {
            throw new NotFoundException('Category');
        }

        if (config('category.auth_middleware')['frontend']['middleware'] !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToDelete($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $this->repository->delete($id);

        event(new CategoryDeletedEvent());

        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (config('category.auth_middleware')['frontend']['middleware'] !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdate($user)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $request->all();

        $categories = $this->entity->whereIn('id', $data['item_ids'])
            ->where('type', $this->type)
            ->get();

        if ($categories->count() == 0) {
            throw new NotFoundException(title_case($this->type) . ' entities');
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
        $category = $this->repository->findWhere(['id' => $id, 'type' => $this->type])->first();
        if (!$category) {
            throw new NotFoundException(title_case($this->type) . ' entity');
        }

        if (config('category.auth_middleware')['frontend']['middleware'] !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $data             = $request->all();
        $category->status = $data['status'];
        $category->save();

        return $this->success();
    }
}
