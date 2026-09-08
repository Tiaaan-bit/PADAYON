<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Therapists extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'specialty',
        'status',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): string
    {
        return $this->remember_token ?? '';
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Relationships
     */
    public function feedbacks()
    {
        return $this->hasMany(TherapistFeedback::class, 'therapist_id');
    }

    public function appointments()
    {
        return $this->hasMany(UsersAppointments::class, 'therapist_id');
    }

    /**
     * Helpers
     */
    public function averageRating()
    {
        return $this->feedbacks()->avg('rating') ?? 0;
    }

    public function ratingsCount()
    {
        return $this->feedbacks()->count();
    }

    public function isActive()
    {
        return $this->status === 'available';
    }
}