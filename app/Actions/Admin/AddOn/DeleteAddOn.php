<?php

namespace App\Actions\Admin\AddOn;

use App\Models\AddOns;
use App\Repositories\Admin\AddOn\AddOnRepositoryInterface;

class DeleteAddOn
{
    public function __construct(
        protected AddOnRepositoryInterface $addOns
    ) {}

    public function execute(AddOns $addOn): bool
    {
        return $this->addOns->delete($addOn);
    }
}