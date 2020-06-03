<?php

namespace VCComponent\Laravel\Category\Traits;

use VCComponent\Laravel\Category\Entities\Category;

trait HasCategoriesTrait
{
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function scopeOfCategory($query, $category_id)
    {
        return $query->whereHas('categories', function ($q) use ($category_id) {
            $q->where('category_id', $category_id);
        });
    }

    public function scopeOfCategories($query, array $category_ids)
    {
        return $query->whereHas('categories', function ($q) use ($category_ids) {
            $q->whereIn('category_id', $category_ids);
        });
    }

    public function attachCategories($category_ids, array $attributes = [])
    {
        $this->categories()->attach($category_ids, $attributes);
    }

    public function detachCategories($category_ids)
    {
        $this->categories()->detach($category_ids);
    }

    public function syncCategories($category_ids)
    {
        $this->categories()->sync($category_ids);
    }
}
