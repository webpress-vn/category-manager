<?php

namespace VCComponent\Laravel\Category\Events;

use Illuminate\Queue\SerializesModels;

class CategoryDeletedEvent
{
    use SerializesModels;

    public function __construct()
    {

    }
}
