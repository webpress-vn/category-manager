<?php

namespace VCComponent\Laravel\Category\Contracts;

use Closure;

interface Pipe
{
    public function handle($content, Closure $next);
}
