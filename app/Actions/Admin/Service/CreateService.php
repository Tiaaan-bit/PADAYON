<?php

namespace App\Actions\Admin\Service;

use App\Models\Services;
use App\Repositories\Admin\Service\ServiceRepositoryInterface;

class CreateService
{
    public function __construct(protected ServiceRepositoryInterface $services) {}

    public function execute(array $data): Services
    {
        return $this->services->create($data);
    }
}
