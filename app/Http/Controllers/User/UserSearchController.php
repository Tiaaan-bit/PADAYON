<?php namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Services;
use App\Models\UsersAppointments;
use App\Models\Therapists;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class UserSearchController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $term = trim((string) $request->input('search', ''));

        $appointments = collect();
        $services = collect();
        $payments = collect();
        $helpTopics = collect();

        if ($term !== '') {
            $like = '%' . addcslashes($term, '\\%_') . '%';

            // The authenticated customer's appointments only.
            $appointments = UsersAppointments::query()
                ->with('service')
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->where(function ($query) use ($like) {
                    $query
                        ->where('appointment_date', 'like', $like)
                        ->orWhere('appointment_time', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhere('payment_status', 'like', $like)
                        ->orWhere('payment_method', 'like', $like)
                        ->orWhereHas('service', function ($serviceQuery) use ($like) {
                            $serviceQuery->where('name', 'like', $like);
                        });
                })
                ->latest('appointment_date')
                ->limit(20)
                ->get();

            // Search services by name or description.
            $services = Services::query()
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)->orWhere('description', 'like', $like);
                })
                ->limit(10)
                ->get();

            // Payment records belong to the customer's own appointments.
            // These are appointment payment details, not another customer's
            // transactions.
            $payments = UsersAppointments::query()
                ->with('service')
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->where(function ($query) use ($like) {
                    $query
                        ->where('payment_status', 'like', $like)
                        ->orWhere('payment_method', 'like', $like)
                        ->orWhere('payment_type', 'like', $like)
                        ->orWhere('amount_paid', 'like', $like)
                        ->orWhereHas('service', function ($serviceQuery) use ($like) {
                            $serviceQuery->where('name', 'like', $like);
                        });
                })
                ->latest('appointment_date')
                ->limit(20)
                ->get();

            // Search common Help topics. Keep these in sync with your
            // actual Help page content.
            $topics = [
                [
                    'title' => 'How to Book an Appointment',
                    'description' => 'Choose a massage service, select an available therapist, and choose your appointment date and time.',
                    'keywords' => 'book booking appointment appointments schedule reservation',
                    'url' => route('user.appointment'),
                ],
                [
                    'title' => 'How to Choose a Therapist',
                    'description' => 'Explore our massage specialists and select an available therapist when booking.',
                    'keywords' => 'therapist therapists massage specialist specialists choose',
                    'url' => route('user.appointment'),
                ],
                [
                    'title' => 'How to cancel or reschedule',
                    'description' => 'Manage an eligible booking through My Appointments.',
                    'keywords' => 'cancel cancellation reschedule change date time',
                ],
                [
                    'title' => 'Payment methods and GCash',
                    'description' => 'Learn about GCash payments, downpayments, and paying at the counter.',
                    'keywords' => 'payment gcash downpayment counter balance',
                ],
                [
                    'title' => 'Appointment and payment status',
                    'description' => 'Understand booking confirmations and payment status updates.',
                    'keywords' => 'pending confirmed paid unpaid failed status',
                ],
                [
                    'title' => 'Massage services and prices',
                    'description' => 'Review available massage services, descriptions, durations, and prices.',
                    'keywords' => 'service massage price duration cost',
                ],
                [
                    'title' => 'Profile and account',
                    'description' => 'Manage your profile and account information.',
                    'keywords' => 'profile account name email phone password',
                ],
            ];

            $helpTopics = collect($topics)
                ->filter(function ($topic) use ($term) {
                    return str_contains(mb_strtolower($topic['title'] . ' ' . $topic['description'] . ' ' . $topic['keywords']), mb_strtolower($term));
                })
                ->values();
        }
        // Search actual therapist records
        $therapists = collect();

        if (filled($term)) {
            $searchTerm = trim($term);

            $searchAllTherapists = collect(['therapist', 'therapists', 'massage', 'specialist', 'specialists'])->contains(fn($keyword) => str_contains(strtolower($searchTerm), $keyword));

            $therapistQuery = Therapists::query();

            // If the user searches for a general therapist-related term,
            // show therapists regardless of their name or specialty.
            if (!$searchAllTherapists) {
                $therapistQuery->where(function ($query) use ($searchTerm) {
                    $query
                        ->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('specialty', 'like', "%{$searchTerm}%")
                        ->orWhere('status', 'like', "%{$searchTerm}%");
                });
            }

            $therapists = $therapistQuery->orderByRaw("CASE WHEN status = 'available' THEN 0 ELSE 1 END")->orderBy('name')->limit(12)->get();
        }

        return view('user.search', compact('term', 'appointments', 'services', 'payments', 'helpTopics', 'therapists'));
    }
}
