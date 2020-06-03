<?php

namespace VCComponent\Laravel\Category\Contracts;

use Illuminate\Http\Request;

interface ViewCategoryListControllerInterface
{
    public function index(Request $request);
}
