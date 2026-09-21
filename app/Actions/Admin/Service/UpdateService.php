<?php

namespace App\Actions\Admin\Service;

use App\Models\Services;
use App\Repositories\Admin\Service\ServiceRepositoryInterface;

class UpdateService
{
    public function __construct(protected ServiceRepositoryInterface $services) {}

    public function execute(Services $service, array $data): Services
    {
        return $this->services->update($service, $data);
    }
}
