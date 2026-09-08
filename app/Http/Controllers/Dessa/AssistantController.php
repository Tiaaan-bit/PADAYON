<?php

namespace App\Http\Controllers\Dessa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function message(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = strtolower(trim($data['message']));

        if (str_contains($message, 'upcoming') || str_contains($message, 'next appointment') || str_contains($message, 'my appointments') || str_contains($message, 'show appointments')) {
            return response()->json([
                'reply' => 'You have upcoming appointments. I can show them now.',
                'action' => 'upcoming',
            ]);
        }

        if (str_contains($message, 'payment') || str_contains($message, 'payments') || str_contains($message, 'payment history') || str_contains($message, 'paid')) {
            return response()->json([
                'reply' => 'I can display your payment history.',
                'action' => 'payments',
            ]);
        }

        if (str_contains($message, 'history') || str_contains($message, 'past appointments') || str_contains($message, 'appointment history')) {
            return response()->json([
                'reply' => 'I can show your appointment history.',
                'action' => 'history',
            ]);
        }

        if (str_contains($message, 'book') || str_contains($message, 'schedule') || str_contains($message, 'make an appointment') || str_contains($message, 'set an appointment')) {
            return response()->json([
                'reply' => 'I can help book an appointment using your saved profile information.',
                'action' => 'book',
            ]);
        }

        if (str_contains($message, 'cancel') || str_contains($message, 'delete appointment') || str_contains($message, 'remove appointment')) {
            return response()->json([
                'reply' => 'I can help cancel an appointment.',
                'action' => 'cancel',
            ]);
        }

        if (str_contains($message, 'reschedule') || str_contains($message, 'move appointment') || str_contains($message, 'change appointment')) {
            return response()->json([
                'reply' => 'I can help reschedule your appointment.',
                'action' => 'reschedule',
            ]);
        }

        if (str_contains($message, 'phone') || str_contains($message, 'email') || str_contains($message, 'contact details') || str_contains($message, 'update profile')) {
            return response()->json([
                'reply' => 'I can help update your contact details.',
                'action' => 'update-profile',
            ]);
        }

        if (str_contains($message, 'time slot') || str_contains($message, 'available time') || str_contains($message, 'open slot')) {
            return response()->json([
                'reply' => 'I can recommend available time slots based on your previous bookings.',
                'action' => 'slots',
            ]);
        }

        if (str_contains($message, 'policy') || str_contains($message, 'service') || str_contains($message, 'services') || str_contains($message, 'how does it work')) {
            return response()->json([
                'reply' => 'I can answer questions about appointments, services, and policies.',
                'action' => 'info',
            ]);
        }

        return response()->json([
            'reply' => 'I can help you book, reschedule, cancel, view upcoming appointments, check payment history, or update your profile.',
            'action' => 'help',
        ]);
    }

    public function book(Request $request)
    {
        return response()->json([
            'reply' => 'Booking flow is ready. Connect this to your appointment creation logic.',
        ]);
    }

    public function upcoming(Request $request)
    {
        return response()->json([
            'reply' => 'Here are your upcoming appointments.',
        ]);
    }

    public function cancel(Request $request)
    {
        return response()->json([
            'reply' => 'Cancellation flow is ready. Connect this to your appointment cancellation logic.',
        ]);
    }

    public function reschedule(Request $request)
    {
        return response()->json([
            'reply' => 'Reschedule flow is ready. Connect this to your appointment rescheduling logic.',
        ]);
    }

    public function history(Request $request)
    {
        return response()->json([
            'reply' => 'Here is your appointment history.',
        ]);
    }

    public function payments(Request $request)
    {
        return response()->json([
            'reply' => 'Here is your payment history.',
        ]);
    }

    public function updateProfile(Request $request)
    {
        return response()->json([
            'reply' => 'Profile update flow is ready.',
        ]);
    }

    public function slots(Request $request)
    {
        return response()->json([
            'reply' => 'Here are the recommended available time slots.',
        ]);
    }
}
