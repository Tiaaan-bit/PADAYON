<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Services</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 font-sans">

    @include('components.sidebar')

    <div class="lg:ml-64 min-h-screen flex flex-col pb-20 lg:pb-0">

        <main class="flex-1 p-4 sm:p-6">

            {{-- Success --}}
            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" />
                    </svg>

                    {{ session('success') }}
                </div>
            @endif

            {{-- Error --}}
            @if (session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                    <p class="font-semibold mb-2">
                        Please fix the following errors:
                    </p>

                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Alpine --}}
            <div
                x-data="{

                    showAddModal: false,
                    showEditModal: false,

                    serviceId: null,

                    name: '',
                    description: '',
                    duration_minutes: '',
                    price: '',
                    status: 'active',

                    openAddModal() {

                        this.showAddModal = true;

                        this.name = '';
                        this.description = '';
                        this.duration_minutes = '';
                        this.price = '';
                        this.status = 'active';

                    },

                    openEditModal(service) {

                        this.showEditModal = true;

                        this.serviceId = service.id;
                        this.name = service.name ?? '';
                        this.description = service.description ?? '';
                        this.duration_minutes = service.duration_minutes ?? '';
                        this.price = service.price ?? '';
                        this.status = service.status ?? 'active';

                    },

                    closeAddModal() {

                        this.showAddModal = false;

                    },

                    closeEditModal() {

                        this.showEditModal = false;

                    }

                }"
            >

                {{-- Page Header --}}
                <div class="mb-6 flex items-center justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Manage Services
                        </h2>

                        <p class="text-sm text-gray-500 mt-0.5">
                            Add, update, and remove appointment services.
                        </p>
                    </div>
                    

                </div>


                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $services->total() }}
                            </p>

                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Total Services
                            </p>

                        </div>

                    </div>


                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $totalActiveServices }}
                            </p>
                            
                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Active
                            </p>

                        </div>

                    </div>


                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $totalInactiveServices }}
                            </p>
                            
                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Inactive
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- ADD SERVICE MODAL --}}
                {{-- ========================================================= --}}

                <div
                    x-show="showAddModal"
                    x-transition
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >

                    {{-- Overlay --}}
                    <div
                        class="absolute inset-0 bg-black/50"
                        @click="closeAddModal()"
                    ></div>


                    {{-- Modal --}}
                    <div
                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden"
                        @click.stop
                    >

                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                            <div>

                                <h3 class="text-lg font-semibold text-gray-800">
                                    Add New Service
                                </h3>

                                <p class="text-xs text-gray-500 mt-0.5">
                                    Create a new appointment service.
                                </p>

                            </div>


                            <button
                                type="button"
                                @click="closeAddModal()"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                        </div>


                        {{-- Add Form --}}
                        <form
                            method="POST"
                            action="{{ route('admin.store') }}"
                            class="p-6 space-y-5"
                        >

                            @csrf


                            {{-- Name + Duration --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Service Name
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        x-model="name"
                                        placeholder="e.g. Bed Massage"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Duration Minutes
                                    </label>

                                    <input
                                        type="number"
                                        name="duration_minutes"
                                        x-model="duration_minutes"
                                        placeholder="Enter duration"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                    @error('duration_minutes')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                            </div>


                            {{-- Description --}}
                            <div>

                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    x-model="description"
                                    rows="4"
                                    placeholder="Short service description"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                ></textarea>

                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- Price + Status --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Price
                                    </label>

                                    <input
                                        type="number"
                                        name="price"
                                        step="0.01"
                                        x-model="price"
                                        placeholder="Enter Price"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Status
                                    </label>

                                    <select
                                        name="status"
                                        x-model="status"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                        <option value="active">
                                            Active
                                        </option>

                                        <option value="inactive">
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Buttons --}}
                            <div class="flex justify-end gap-3 pt-2">

                                <button
                                    type="button"
                                    @click="closeAddModal()"
                                    class="rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition"
                                >
                                    Cancel
                                </button>


                                <button
                                    type="submit"
                                    class="rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6F4E37] transition"
                                >
                                    Save Service
                                </button>

                            </div>

                        </form>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- FILTER --}}
                {{-- ========================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                    <form
                        method="GET"
                        action="{{ route('admin.services') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4"
                    >

                        <div class="md:col-span-3">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by service name, description, or duration"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >

                        </div>


                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="rounded-lg bg-[#849753] px-5 py-2 text-white hover:bg-[#6F4E37] text-sm w-32"
                            >
                                Filter
                            </button>


                            <a
                                href="{{ route('admin.services') }}"
                                class="rounded-lg border border-gray-300 px-5 py-2 text-sm text-gray-700 hover:bg-gray-50 w-32 text-center"
                            >
                                Reset
                            </a>

                        </div>

                    </form>

                </div>


                {{-- ========================================================= --}}
                {{-- ALL SERVICES --}}
                {{-- ========================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

                        <h3 class="font-semibold text-gray-800">
                            All Services
                        </h3>

                        <button
                        type="button"
                        @click="openAddModal()"
                        class="text-xs font-semibold px-4 py-2.5 rounded-lg bg-[#849753] text-white hover:bg-[#6F4E37] transition"
                    >
                        Add New Service
                    </button>

                    </div>


                    @if ($services->isEmpty())

                        <div class="py-16 text-center text-gray-400">

                            <svg
                                class="w-12 h-12 mx-auto mb-3 text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>

                            <p class="text-sm">
                                No services found.
                            </p>

                        </div>

                    @else

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm">

                                <thead>

                                    <tr class="bg-gray-50 text-left">

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            ID
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Service Name
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Description
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Duration
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Price
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Status
                                        </th>

                                        <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-gray-100">

                                    @foreach ($services as $service)

                                        <tr class="hover:bg-gray-50 transition">

                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                                {{ $service->id }}
                                            </td>


                                            <td class="px-5 py-4">

                                                <p class="font-semibold text-gray-800 whitespace-nowrap">
                                                    {{ $service->name }}
                                                </p>

                                            </td>


                                            <td class="px-5 py-4 text-gray-600">
                                                {{ $service->description }}
                                            </td>


                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                                {{ $service->duration_minutes }} Minutes
                                            </td>


                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                                ₱{{ number_format((float) $service->price, 2) }}
                                            </td>


                                            <td class="px-5 py-4">

                                                @if ($service->status === 'active')

                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                        Active

                                                    </span>

                                                @else

                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>

                                                        Inactive

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- Actions --}}
                                            <td class="px-5 py-4">

                                                <div class="flex items-center gap-2">

                                                    {{-- MODIFY --}}
                                                    <button
                                                        type="button"
                                                        @click="openEditModal({{ \Illuminate\Support\Js::from([
                                                            'id' => $service->id,
                                                            'name' => $service->name,
                                                            'description' => $service->description,
                                                            'duration_minutes' => $service->duration_minutes,
                                                            'price' => $service->price,
                                                            'status' => $service->status,
                                                        ]) }})"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition whitespace-nowrap"
                                                    >
                                                        Modify
                                                    </button>


                                                    {{-- DELETE --}}
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.destroy', $service) }}"
                                                    >

                                                        @csrf

                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('Delete {{ addslashes($service->name) }}? This cannot be undone.')"
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


                        @if ($services->hasPages())

                            <div class="px-5 py-4 border-t border-gray-100 flex justify-end">

                                {{ $services->links() }}

                            </div>

                        @endif

                    @endif

                </div>


                {{-- ========================================================= --}}
                {{-- MODIFY SERVICE MODAL --}}
                {{-- ========================================================= --}}

                <div
                    x-show="showEditModal"
                    x-transition
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >

                    {{-- Overlay --}}
                    <div
                        class="absolute inset-0 bg-black/50"
                        @click="closeEditModal()"
                    ></div>


                    {{-- Modal --}}
                    <div
                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden"
                        @click.stop
                    >

                        {{-- Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                            <div>

                                <h3 class="text-lg font-semibold text-gray-800">
                                    Modify Service
                                </h3>

                                <p class="text-xs text-gray-500 mt-0.5">
                                    Update the selected appointment service.
                                </p>

                            </div>


                            <button
                                type="button"
                                @click="closeEditModal()"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                            >

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>

                            </button>

                        </div>


                        {{-- Edit Form --}}
                        <form
                            :action="'{{ url('/admin/services') }}/' + serviceId"
                            method="POST"
                            class="p-6 space-y-5"
                        >

                            @csrf

                            @method('PUT')


                            {{-- Name + Duration --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Service Name
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        x-model="name"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                </div>


                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Duration Minutes
                                    </label>

                                    <input
                                        type="number"
                                        name="duration_minutes"
                                        x-model="duration_minutes"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                </div>

                            </div>


                            {{-- Description --}}
                            <div>

                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    x-model="description"
                                    rows="4"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                ></textarea>

                            </div>


                            {{-- Price + Status --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Price
                                    </label>

                                    <input
                                        type="number"
                                        name="price"
                                        step="0.01"
                                        x-model="price"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                </div>


                                <div>

                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Status
                                    </label>

                                    <select
                                        name="status"
                                        x-model="status"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                    >

                                        <option value="active">
                                            Active
                                        </option>

                                        <option value="inactive">
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Buttons --}}
                            <div class="flex justify-end gap-3 pt-2">

                                <button
                                    type="button"
                                    @click="closeEditModal()"
                                    class="rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition"
                                >
                                    Cancel
                                </button>


                                <button
                                    type="submit"
                                    class="rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6F4E37] transition"
                                >
                                    Update Service
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </main>

    </div>

</body>

</html>