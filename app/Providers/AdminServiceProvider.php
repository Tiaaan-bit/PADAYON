<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Policies\Admin\UserPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Models\UsersAppointments;
use App\Policies\Admin\PostPolicy;
use App\Policies\Admin\UsersAppointmentsPolicy;
use App\Models\Services;
use App\Policies\Admin\ServicePolicy;

use App\Models\AddOns;
use App\Policies\Admin\AddOnPolicy;

use App\Repositories\Admin\Service\ServiceRepository;
use App\Repositories\Admin\Service\ServiceRepositoryInterface;

use App\Repositories\Admin\Dashboard\DashboardRepository;
use App\Repositories\Admin\Dashboard\DashboardRepositoryInterface;

use App\Repositories\Admin\Appointment\AppointmentRepository;
use App\Repositories\Admin\Appointment\AppointmentRepositoryInterface;

use App\Repositories\Admin\User\UserRepository;
use App\Repositories\Admin\User\UserRepositoryInterface;

use App\Repositories\Admin\AddOn\AddOnRepository;
use App\Repositories\Admin\AddOn\AddOnRepositoryInterface;

use App\Repositories\Admin\Therapist\TherapistRepository;
use App\Repositories\Admin\Therapist\TherapistRepositoryInterface;

use App\Models\Therapists;
use App\Policies\Admin\TherapistPolicy;

use App\Repositories\Admin\Transaction\TransactionRepository;
use App\Repositories\Admin\Transaction\TransactionRepositoryInterface;

use App\Repositories\Admin\Report\ReportRepository;
use App\Repositories\Admin\Report\ReportRepositoryInterface;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);

        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);

        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(AddOnRepositoryInterface::class, AddOnRepository::class);

        $this->app->bind(TherapistRepositoryInterface::class, TherapistRepository::class);

        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);

        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);

        Gate::policy(UsersAppointments::class, UsersAppointmentsPolicy::class);

        Gate::policy(User::class, UserPolicy::class);

        Gate::policy(Services::class, ServicePolicy::class);

        Gate::policy(AddOns::class, AddOnPolicy::class);

        Gate::policy(Therapists::class, TherapistPolicy::class);
    }
}
