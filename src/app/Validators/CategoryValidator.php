<?php

namespace VCComponent\Laravel\Category\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;
use VCComponent\Laravel\Vicoders\Core\Validators\ValidatorInterface;

class CategoryValidator extends AbstractValidator
{
    protected $rules = [
        ValidatorInterface::RULE_ADMIN_CREATE  => [
            'name' => ['required'],
        ],
        ValidatorInterface::RULE_ADMIN_UPDATE  => [
            'name' => ['required'],
        ],
        ValidatorInterface::RULE_CREATE        => [
            'name' => ['required'],
        ],
        ValidatorInterface::RULE_UPDATE        => [
            'name' => ['required'],
        ],
        ValidatorInterface::BULK_UPDATE_STATUS => [
            'item_ids' => ['required'],
            'status'   => ['required'],
        ],
        ValidatorInterface::UPDATE_STATUS_ITEM => [
            'status' => ['required'],
        ],
    ];
}
