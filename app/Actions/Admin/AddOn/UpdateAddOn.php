<?php

namespace App\Actions\Admin\AddOn;

use App\Models\AddOns;
use App\Repositories\Admin\AddOn\AddOnRepositoryInterface;

class UpdateAddOn
{
    public function __construct(
        protected AddOnRepositoryInterface $addOns
    ) {}

    public function execute(AddOns $addOn, array $data): AddOns
    {
        return $this->addOns->update($addOn, $data);
    }
}