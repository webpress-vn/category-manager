<?php

namespace VCComponent\Laravel\Category\Events;

use Illuminate\Queue\SerializesModels;

class CategoryCreatedEvent
{
    use SerializesModels;

    public $category;

    public function __construct($category)
    {
        $this->category = $category;
    }
}
