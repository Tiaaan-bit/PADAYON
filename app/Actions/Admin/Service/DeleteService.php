<?php

namespace App\Actions\Admin\Service;

use App\Models\Services;
use App\Repositories\Admin\Service\ServiceRepositoryInterface;

class DeleteService
{
    public function __construct(protected ServiceRepositoryInterface $services) {}

    public function execute(Services $service): bool
    {
        return $this->services->delete($service);
    }
}
