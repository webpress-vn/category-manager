<?php

namespace VCComponent\Laravel\Category\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CategoryRepository.
 *
 * @package namespace VCComponent\Laravel\Category\Repositories;
 */
interface CategoryRepository extends RepositoryInterface
{
    public function getWithPagination($filters);
}
