<?php

namespace VCComponent\Laravel\Category\ViewModels\CategoryList;

use VCComponent\Laravel\Category\ViewModels\CategoryDetail\CategoryDetailViewModelInterface;

class CategoryDetailViewModel implements CategoryDetailViewModelInterface
{
    public function test()
    {
        return '<h1>test Category detail hook</h1>';
    }
}
