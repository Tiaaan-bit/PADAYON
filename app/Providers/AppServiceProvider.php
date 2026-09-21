<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\UsersAppointments;
use App\Policies\Admin\PostPolicy;
use App\Policies\Admin\UsersAppointmentsPolicy;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

use App\Models\User;
use App\Policies\Admin\UserPolicy;

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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(10, 5)
                ->by(strtolower($request->input('email')) . '|' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? 600;

                    return back()
                        ->withErrors([
                            'email' => 'Too many login attempts, Try again later.',
                        ])
                        ->with('login_rate_limited', true)
                        ->with('retry_after', now()->addSeconds((int) $retryAfter)->timestamp)
                        ->withInput();
                });
        });

        Gate::policy(Post::class, PostPolicy::class);

        Gate::policy(UsersAppointments::class, UsersAppointmentsPolicy::class);

        Gate::policy(User::class, UserPolicy::class);

        Gate::policy(Services::class, ServicePolicy::class);

        Gate::policy(AddOns::class, AddOnPolicy::class);

        Gate::policy(Therapists::class, TherapistPolicy::class);
    }
}
