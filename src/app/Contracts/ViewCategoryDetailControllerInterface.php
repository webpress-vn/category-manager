<?php

namespace VCComponent\Laravel\Category\Contracts;

use Illuminate\Http\Request;

interface ViewCategoryDetailControllerInterface
{
    public function show($id, Request $request);
}
