<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans">

    @include('components.sidebar')

    <div class="lg:ml-64 min-h-screen flex flex-col pb-20 lg:pb-0">
        

        <main class="flex-1 p-4 sm:p-6">
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Manage Customers</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage all registered users and monitor activity.</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Total Users</p>
                    </div>
                </div>
            
                <!-- Active -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['active'] }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Active</p>
                    </div>
                </div>
            
                <!-- Pending -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Pending</p>
                    </div>
                </div>
            
                <!-- Online Now -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['online'] }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Online Now</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">
                <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, email, or phone"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
            
                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
            
                    <div class="w-full sm:w-52">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Joined Date</label>
                        <input
                            type="date"
                            name="joined_date"
                            value="{{ request('joined_date') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
            
                    <div class="flex gap-2">
                        <button type="submit" class="rounded-lg bg-[#849753] px-4 py-2 text-white hover:bg-[#6F4E37] text-sm">
                            Filter
                        </button>
            
                        <a href="{{ route('admin.users') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">All Users</h3>
                    <span class="text-xs text-gray-400">{{ $users->total() }} total</span>
                </div>

                @if($users->isEmpty())
                    <div class="py-16 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z"/>
                        </svg>
                        <p class="text-sm">No users registered yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left">
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">User</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Phone</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Status</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Online</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Last Seen</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Joined</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-[#849753] flex items-center justify-center text-white font-bold text-sm shrink-0">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $user->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">{{ $user->phone ?? '—' }}</td>

                                        <td class="px-5 py-4">
                                            @if($user->status === 'active')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Pending
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($user->is_online)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Online
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Offline
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">
                                            {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : '—' }}
                                        </td>

                                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>

                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('Change status for {{ addslashes($user->name) }}?')"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg transition whitespace-nowrap
                                                            {{ $user->status === 'active'
                                                                ? 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100'
                                                                : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}"
                                                    >
                                                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition whitespace-nowrap"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 flex justify-end">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </main>
    </div>
</body>
</html>