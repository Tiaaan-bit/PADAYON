<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $posts = Post::query()
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(5)
            ->get();

            

        return view('user.notifications', [
            'posts' => $posts,

            'unreadNotifications' => $user->unreadNotifications()
                ->latest()
                ->get(),

            'readNotifications' => $user->readNotifications()
                ->latest()
                ->get(),
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return back()->with('success', 'Notification deleted successfully.');
    }
}