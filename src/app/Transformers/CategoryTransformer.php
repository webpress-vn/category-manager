<?php

namespace VCComponent\Laravel\Category\Transformers;

use App\Transformers\SeoMetaTransformer;
use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Menu\Transformers\ItemMenuTransformer;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'children',
        'parent',
        'seoMeta',
        'menus',
        'products',
    ];
    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        $transform = [
            'id'        => (int) $model->id,
            'parent_id' => (int) $model->parent_id,
            'name'      => $model->name,
            'slug'      => $model->slug,
            'type'      => $model->type,
            'status'    => $model->status,
            'hot'       => $model->hot,
        ];

        $transform['timestamps'] = [
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];

        return $transform;
    }

    public function includeChildren($model)
    {
        return $this->collection($model->children, new self);
    }

    public function includeParent($model)
    {
        if ($model->parent) {
            return $this->item($model->parent, new self);
        }
        return $this->null();
    }

    public function includeSeoMeta($model)
    {
        if ($model->seoMeta) {
            return $this->item($model->seoMeta, new SeoMetaTransformer());
        }
        return $this->null();
    }

    public function includeMenus($model)
    {
        return $this->collection($model->menus, new ItemMenuTransformer());
    }
}
