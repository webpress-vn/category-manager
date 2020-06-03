<?php

namespace VCComponent\Laravel\Category\Contracts;

interface CategoryManagement
{
    public function ableToShow($user, $id);
    public function ableToCreate($user);
    public function ableToUpdate($user);
    public function ableToUpdateItem($user, $id);
    public function ableToDelete($user, $id);
}
