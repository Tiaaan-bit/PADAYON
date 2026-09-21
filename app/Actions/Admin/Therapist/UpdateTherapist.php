<?php

namespace App\Actions\Admin\Therapist;

use App\Models\Therapists;
use App\Repositories\Admin\Therapist\TherapistRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateTherapist
{
    public function __construct(protected TherapistRepositoryInterface $therapists) {}

    public function execute(Therapists $therapist, array $data): Therapists
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($therapist->image) {
                Storage::disk('public')->delete($therapist->image);
            }

            $data['image'] = $data['image']->store('therapists', 'public');
        }

        return $this->therapists->update($therapist, $data);
    }
}
