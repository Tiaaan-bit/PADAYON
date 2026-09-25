<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Admin\Appointment\AppointmentStatus;

class UsersAppointments extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'user_id',
        'service_id',
        'therapist_id',
        'service_price',
        'service_duration_minutes',
        'level',
        'add_on_id',
        'addons_price',
        'addons_duration_minutes',
        'has_previous_operations',
        'body_problem',
        'appointment_date',
        'appointment_time',
        'appointment_end_time',
        'payment_method',
        'payment_type',
        'amount_paid',

        'paymongo_checkout_session_id',
        'paymongo_payment_id',
        'payment_amount',
        'payment_status',
        'paid_at',
        'paymongo_reference_number',
        'paymongo_checkout_expires_at',

        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date:Y-m-d',
        'service_price' => 'decimal:2',
        'addons_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'paymongo_checkout_expires_at' => 'datetime',
        'status' => AppointmentStatus::class,

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function therapist()
    {
        return $this->belongsTo(Therapists::class, 'therapist_id');
    }

    public function addOn()
    {
        return $this->belongsTo(AddOns::class, 'add_on_id');
    }
}