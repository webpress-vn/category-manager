<?php

namespace VCComponent\Laravel\Category\Traits;

trait Helpers
{
    private function applyQueryScope($query, $field, $value)
    {
        $query = $query->where($field, $value);

        return $query;
    }

    private function handlingPathArray($path_array, $base)
    {
        switch ($path_array->count()) {
            case $base + 1:
                $path_array->pop();
                break;
            case $base + 2:
                $path_array->pop();
                $path_array->pop();
                break;
        }

        return $path_array;
    }
}
