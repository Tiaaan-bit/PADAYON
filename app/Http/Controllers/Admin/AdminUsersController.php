<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function users(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('joined_date')) {
            $query->whereDate('created_at', $request->joined_date);
        }

        $users = $query->orderByDesc('is_online')->orderByDesc('last_seen_at')->paginate(5)->withQueryString();

        $stats = [
            'total' => User::where('role', 'user')->count(),
            'active' => User::where('role', 'user')->where('status', 'active')->count(),
            'pending' => User::where('role', 'user')->where('status', 'pending')->count(),
            'online' => User::where('role', 'user')->where('is_online', true)->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot modify admin accounts.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'pending' : 'active',
        ]);

        return back()->with('success', "User status updated to {$user->fresh()->status}.");
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin accounts.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
