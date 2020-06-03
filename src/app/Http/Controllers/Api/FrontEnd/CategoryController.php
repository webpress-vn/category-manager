<?php

namespace VCComponent\Laravel\Category\Http\Controllers\Api\Frontend;

use VCComponent\Laravel\Category\Traits\CategoryFrontendMethods;
use VCComponent\Laravel\Category\Traits\Helpers;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class CategoryController extends ApiController
{
    use CategoryFrontendMethods, Helpers;

    protected $repository;
    protected $entity;
    protected $validator;
    protected $transformer;
}
