<?php

namespace App\Repositories\Admin\User;

interface UserRepositoryInterface
{
    public function getUsersPageData(array $filters): array;

    public function getCustomerAppointmentData(int $userId): array;
}
