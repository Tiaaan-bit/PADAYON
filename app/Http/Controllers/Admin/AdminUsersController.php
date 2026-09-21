<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\User\DeleteUser;
use App\Actions\Admin\User\ToggleUserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Models\User;
use App\Repositories\Admin\User\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminUsersController extends Controller
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function users(UserFilterRequest $request): View
    {
        $data = $this->userRepository->getUsersPageData($request->validated());

        return view('admin.users', $data);
    }

    public function toggleStatus(User $user, ToggleUserStatus $toggleUserStatus): RedirectResponse
    {
        Gate::authorize('toggleStatus', $user);

        $toggleUserStatus->execute($user);

        return back()->with('success', "User status updated to {$user->status->value}.");
    }

    public function destroy(User $user, DeleteUser $deleteUser): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $deleteUser->execute($user);

        return back()->with('success', 'User deleted successfully.');
    }
}
