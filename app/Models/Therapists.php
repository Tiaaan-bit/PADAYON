<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Therapists extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'therapists';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'password', 'description', 'specialty', 'status', 'image'];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function feedbacks()
    {
        return $this->hasMany(TherapistFeedback::class, 'therapist_id');
    }

    public function appointments()
    {
        return $this->hasMany(UsersAppointments::class, 'therapist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function averageRating(): float
    {
        return (float) ($this->feedbacks()->avg('rating') ?? 0);
    }

    public function ratingsCount(): int
    {
        return $this->feedbacks()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'available';
    }
}
