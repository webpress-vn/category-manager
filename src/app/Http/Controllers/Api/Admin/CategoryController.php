<?php

namespace VCComponent\Laravel\Category\Http\Controllers\Api\Admin;

use VCComponent\Laravel\Category\Traits\CategoryAdminMethods;
use VCComponent\Laravel\Category\Traits\Helpers;
use VCComponent\Laravel\Language\Traits\HasLanguageTrait;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class CategoryController extends ApiController
{
    use CategoryAdminMethods, Helpers , HasLanguageTrait;

    protected $repository;
    protected $entity;
    protected $validator;
    protected $transformer;
    protected $constraint;
}
