<?php

namespace VCComponent\Laravel\Category\Categories\Contracts;

interface Category
{
    public function __construct();

    public function withRelationPaginate($column, $value, $relations, $perPage);
}
