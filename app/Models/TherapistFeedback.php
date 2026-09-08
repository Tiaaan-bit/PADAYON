<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'therapist_id',
        'appointment_id',
        'rating',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapists::class, 'therapist_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(UsersAppointments::class, 'appointment_id');
    }
}