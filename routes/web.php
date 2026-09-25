<?php

use App\Http\Controllers\Admin\AdminAddOnsController;
use App\Http\Controllers\Admin\AdminAppointmentsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\AdminServicesController;
use App\Http\Controllers\Admin\AdminTherapistController;
use App\Http\Controllers\Admin\AdminTransactionsController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomePage\PageController;
use App\Http\Controllers\Staff\StaffAppointmentsController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffTherapistController;
use App\Http\Controllers\Staff\StaffTransactionsController;
use App\Http\Controllers\Therapist\TherapistDashboardController;
use App\Http\Controllers\User\MyAppointmentController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PaymentHistoryController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\TherapistsController;
use App\Http\Controllers\User\UserAppointmentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\Payment\PayMongoWebhookController;

Schedule::command('appointments:expire-pending-payments')->everyMinute();


// ── Redirect root ─────────────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'showHomePage'])->name('home.showHomePage');
Route::get('/about', [PageController::class, 'showAboutPage'])->name('home.showAboutPage');
Route::get('/contacts', [PageController::class, 'showContactPage'])->name('home.showContactPage');
Route::get('/services', [PageController::class, 'showServicesPage'])->name('home.showServicesPage');

// ── Guest routes ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.store');
    
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// ── Email verification (auth, any role) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verify/code', [EmailVerificationController::class, 'verifyCode'])->name('verification.verify-code');
    Route::post('/email/verify/resend-code', [EmailVerificationController::class, 'resend'])->name('verification.resend-code');
});

// ── User routes (auth.user middleware = verified users only) ──────────────────
Route::middleware('auth.user')->prefix('user')->name('user.')->group(function () {

        //---User Dashboard---//
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        //---User Appointment---//
        Route::get('/appointment', [UserAppointmentController::class, 'index'])->name('appointment');
        Route::get('/appointments/create', [UserAppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/store', [UserAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/available-slots', [UserAppointmentController::class, 'availableSlots'])->name('appointments.availableSlots');
        Route::get('/appointments/{appointment}/payment/success', [UserAppointmentController::class,'paymentSuccess',])->name('appointments.payment.success');
        Route::get('/appointments/{appointment}/payment/cancel', [UserAppointmentController::class,'paymentCancel',])->name('appointments.payment.cancel');
        Route::post('/appointments/{appointment}/pay-again',[UserAppointmentController::class, 'payAgain'])->name('appointments.payment.retry');

        //---User MyAppointment---//
        Route::get('/my-appointments', [MyAppointmentController::class, 'index'])->name('my-appointments');
        Route::put('/my-appointments/{appointment}/cancel', [MyAppointmentController::class, 'cancel'])->name('my-appointments.cancel');

        //---User Payment History---//
        Route::get('/payment-history', [PaymentHistoryController::class, 'index'])->name('payment-history');

        //---User Notifications---//
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

         //---User Therapist---//
        Route::get('/therapists', [TherapistsController::class, 'index'])->name('therapists.index');
        Route::post('/therapists/{therapist}/feedback', [TherapistsController::class, 'storeFeedback'])->name('therapists.feedback');

        //---User Profile---//
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });


    Route::post('/webhooks/paymongo', [PayMongoWebhookController::class,'handle',])->name('webhooks.paymongo');



// ── Admin routes (auth.admin middleware = admins only) ────────────────────────
Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {

        //---Admin Dashboard Controller---//
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        //---Admin Post Controller---//
        Route::get('/posts', [PostController::class, 'posts'])->name('posts');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        //---Admin User Controller---//
        Route::get('/users', [AdminUsersController::class, 'users'])->name('users');
        Route::patch('/users/{user}/status', [AdminUsersController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');

        //---Admin Services Controller---//
        Route::get('/services', [AdminServicesController::class, 'services'])->name('services');
        Route::post('/services', [AdminServicesController::class, 'store'])->name('store');
        Route::get('/services/{service}/edit', [AdminServicesController::class, 'edit'])->name('edit');
        Route::put('/services/{service}', [AdminServicesController::class, 'update'])->name('update');
        Route::delete('/services/{service}', [AdminServicesController::class, 'destroy'])->name('destroy');

        //---Admin Add-Ons Controller---//
        Route::get('/addons', [AdminAddOnsController::class, 'addons'])->name('addons');
        Route::post('/addons', [AdminAddOnsController::class, 'store'])->name('addons.store');
        Route::get('/addons/{addOn}/edit', [AdminAddOnsController::class, 'edit'])->name('addons.edit');
        Route::put('/addons/{addOn}', [AdminAddOnsController::class, 'update'])->name('addons.update');
        Route::delete('/addons/{addOn}', [AdminAddOnsController::class, 'destroy'])->name('addons.destroy');

        //---Admin Therapist Controller---//
        Route::get('/therapists', [AdminTherapistController::class, 'index'])->name('therapists');
        Route::post('/therapists', [AdminTherapistController::class, 'store'])->name('therapist.store');
        Route::put('/therapists/{therapist}', [AdminTherapistController::class, 'update'])->name('therapist.update');
        Route::delete('/therapists/{therapist}', [AdminTherapistController::class, 'destroy'])->name('therapist.destroy');
        Route::get( '/therapists/{therapist}/feedback',[AdminTherapistController::class, 'feedback'])->name('therapist.feedback');

        //---Admin Appointment Controller---//
        Route::get('/appointments', [AdminAppointmentsController::class, 'index'])->name('appointments');
        Route::put('/appointments/{appointment}/status', [AdminAppointmentsController::class, 'updateStatus'])->name('appointments.updateStatus');
        Route::put('/appointments/{appointment}/cancel', [AdminAppointmentsController::class, 'cancel'])->name('appointments.cancel');

        //---Admin Transaction Controller---//
        Route::get('/transactions', [AdminTransactionsController::class, 'transactions'])->name('transactions');

        //---Admin Reports Controller---//
        Route::get('/reports', [AdminReportsController::class, 'reports'])->name('reports');
    });
    

    Route::middleware('auth.therapist')->prefix('therapist')->name('therapist.')->group(function () {

        Route::get('/dashboard', [TherapistDashboardController::class, 'dashboard'])->name('dashboard');
    });



    Route::middleware(['auth.staff'])->prefix('staff')->name('staff.')->group(function () {

        Route::get('/dashboard',[StaffDashboardController::class, 'dashboard'])->name('dashboard');


        Route::get('/appointments', [StaffAppointmentsController::class, 'index'])->name('appointments');
        Route::put('/appointments/{appointment}/status', [StaffAppointmentsController::class, 'updateStatus'])->name('appointments.updateStatus');
        Route::put('/appointments/{appointment}/cancel', [StaffAppointmentsController::class, 'cancel'])->name('appointments.cancel');


        Route::get('/transactions', [StaffTransactionsController::class, 'transactions'])->name('transactions');


        Route::get('/therapists', [StaffTherapistController::class, 'index'])->name('therapists');
        Route::put('/therapists/{therapist}', [StaffTherapistController::class, 'update'])->name('therapist.update');



        
    });

// ── Logout ────────────────────────────────────────────────────────────────────
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



