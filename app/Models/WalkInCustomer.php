<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WalkInCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'registered_user_id', 'registration_token', 'registration_token_expires_at'];

    protected $casts = [
        'registration_token_expires_at' => 'datetime',
    ];

    public function registeredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(UsersAppointments::class, 'walk_in_customer_id');
    }
}
