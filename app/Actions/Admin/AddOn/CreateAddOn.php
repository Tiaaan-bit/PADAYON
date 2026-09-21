<?php

namespace App\Actions\Admin\AddOn;

use App\Models\AddOns;
use App\Repositories\Admin\AddOn\AddOnRepositoryInterface;

class CreateAddOn
{
    public function __construct(
        protected AddOnRepositoryInterface $addOns
    ) {}

    public function execute(array $data): AddOns
    {
        return $this->addOns->create($data);
    }
}