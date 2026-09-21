<?php

namespace App\Actions\Admin\Therapist;

use App\Models\Therapists;
use App\Repositories\Admin\Therapist\TherapistRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class CreateTherapist
{
    public function __construct(protected TherapistRepositoryInterface $therapists) {}

    public function execute(array $data): Therapists
    {
        $data['password'] = Hash::make($data['password']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('therapists', 'public');
        }

        return $this->therapists->create($data);
    }
}
