<?php

namespace App\Providers;

use App\Models\Therapists;
use App\Models\User;
use App\Models\UsersAppointments;
use App\Repositories\Staff\Appointment\StaffAppointmentRepository;
use App\Repositories\Staff\Appointment\StaffAppointmentRepositoryInterface;
use App\Repositories\Staff\Dashboard\StaffDashboardRepository;
use App\Repositories\Staff\Dashboard\StaffDashboardRepositoryInterface;
use App\Repositories\Staff\Therapist\StaffTherapistRepository;
use App\Repositories\Staff\Therapist\StaffTherapistRepositoryInterface;
use App\Repositories\Staff\Transaction\StaffTransactionRepository;
use App\Repositories\Staff\Transaction\StaffTransactionRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class StaffServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StaffDashboardRepositoryInterface::class, StaffDashboardRepository::class);

        $this->app->bind(StaffAppointmentRepositoryInterface::class, StaffAppointmentRepository::class);

        $this->app->bind(StaffTransactionRepositoryInterface::class, StaffTransactionRepository::class);

        $this->app->bind(StaffTherapistRepositoryInterface::class, StaffTherapistRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        Gate::define('staff.therapist.viewAny', function (User $user): bool {
            return $user->isStaff();
        });

        Gate::define('staff.therapist.view', function (User $user, Therapists $therapist): bool {
            return $user->isStaff();
        });

        Gate::define('staff.therapist.update', function (User $user, Therapists $therapist): bool {
            return $user->isStaff();
        });




        Gate::define('staff.appointment.updateStatus', function (User $user, UsersAppointments $appointment): bool {
            return $user->isStaff();
        });

        Gate::define('staff.appointment.cancel', function (User $user, UsersAppointments $appointment): bool {
            return $user->isStaff();
        });
    }
}
