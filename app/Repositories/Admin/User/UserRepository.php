<?php

namespace App\Repositories\Admin\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Enums\Admin\User\UserRole;
use App\Enums\Admin\User\UserStatus;
use App\Models\User;
use App\Models\UsersAppointments;
use App\Repositories\Admin\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUsersPageData(array $filters): array
    {
        $query = User::query()->where('role', UserRole::USER);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['joined_date'])) {
            $query->whereDate('created_at', $filters['joined_date']);
        }

        $users = $query->orderByDesc('is_online')->orderByDesc('last_seen_at')->paginate(5)->withQueryString();

        $stats = [
            'total' => User::where('role', UserRole::USER)->count(),

            'active' => User::where('role', UserRole::USER)->where('status', UserStatus::ACTIVE)->count(),

            'pending' => User::where('role', UserRole::USER)->where('status', UserStatus::PENDING)->count(),

            'online' => User::where('role', UserRole::USER)->where('is_online', true)->count(),
        ];

        return compact('users', 'stats');
    }

    public function getCustomerAppointmentData(int $userId): array
    {
        $customer = User::query()
            ->where('role', UserRole::USER)
            ->findOrFail($userId);

        $query = UsersAppointments::query()
            ->where('user_id', $customer->id);

        // Appointment statistics
        $stats = [
            'total' => (clone $query)->count(),

            'pending' => (clone $query)
                ->where('status', AppointmentStatus::PENDING->value)
                ->count(),

            'confirmed' => (clone $query)
                ->where('status', AppointmentStatus::CONFIRMED->value)
                ->count(),

            'rejected' => (clone $query)
                ->where('status', AppointmentStatus::REJECTED->value)
                ->count(),

            'cancelled' => (clone $query)
                ->where('status', AppointmentStatus::CANCELLED->value)
                ->count(),

            'no_show' => (clone $query)
                ->where('status', AppointmentStatus::NO_SHOW->value)
                ->count(),

            'failed' => (clone $query)
                ->where('status', AppointmentStatus::FAILED->value)
                ->count(),
        ];

        // Paginated appointment history
        $appointments = (clone $query)
            ->with(['service', 'therapist'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10);

        // Transform appointment records
        $appointments->through(function ($appointment) {
            $status = $appointment->status;

            return [
                'id' => $appointment->id,

                'service' => $appointment->service?->name ?? '—',

                'description' => $appointment->service?->description ?? '—',

                'duration' => $appointment->service?->duration_minutes ?? '—',

                'therapist' => $appointment->therapist?->name ?? '—',

                'date' => $appointment->appointment_date?->format('Y-m-d'),

                'time' => $appointment->appointment_time
                    ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                    : '—',

                'end_time' => $appointment->appointment_end_time
                    ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
                    : '—',

                'price' => $appointment->service_price,

                'status' => $status instanceof AppointmentStatus
                    ? $status->value
                    : $status,

                'status_label' => $status instanceof AppointmentStatus
                    ? $status->label()
                    : (string) $status,
            ];
        });

        return [
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'joined' => $customer->created_at?->format('M d, Y'),
            ],

            'stats' => $stats,

            'appointments' => $appointments,
        ];
    }
}
