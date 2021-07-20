<?php

namespace VCComponent\Laravel\Category\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Category\Contracts\CategoryManagement;
use VCComponent\Laravel\Category\Contracts\CategorySchema;
use VCComponent\Laravel\Category\Traits\CategoryManagementTrait;
use VCComponent\Laravel\Category\Traits\CategorySchemaTrait;

class Category extends Model implements Transformable, CategorySchema, CategoryManagement
{
    use TransformableTrait, CategorySchemaTrait, CategoryManagementTrait, Sluggable, SluggableScopeHelpers;

    const STATUS_PENDING = 2;
    const STATUS_ACTIVE  = 1;

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'description',
        'status',
        'order',
        'is_hot',
        'thumbnail',
    ];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
