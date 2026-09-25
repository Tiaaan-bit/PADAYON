<?php

namespace App\Models;

use App\Models\Post;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Admin\User\UserRole;
use App\Enums\Admin\User\UserStatus;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'status', 'is_online', 'last_seen_at', 'email_verified_at', 'email_verification_code', 'email_verification_code_expires_at'];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
        'is_online' => 'boolean',
        'email_verification_code_expires_at' => 'datetime',
        'role' => UserRole::class,
        'status' => UserStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    public function sendEmailVerificationNotification(): void
    {
        $code = (string) random_int(100000, 999999);

        $this->forceFill([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(5),
        ])->save();

        $this->notify(new CustomVerifyEmail($code));
    }

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token));
    }

    /*
    |--------------------------------------------------------------------------
    | Online Status
    |--------------------------------------------------------------------------
    */

    public function markOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function markOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
