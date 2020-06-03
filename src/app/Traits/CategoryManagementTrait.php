<?php

namespace VCComponent\Laravel\Category\Traits;

trait CategoryManagementTrait
{
    public function ableToShow($user, $id)
    {
        return true;
    }

    public function ableToCreate($user)
    {
        return true;
    }

    public function ableToUpdate($user)
    {
        return true;
    }

    public function ableToUpdateItem($user, $id)
    {
        return true;
    }

    public function ableToDelete($user, $id)
    {
        return true;
    }
}
