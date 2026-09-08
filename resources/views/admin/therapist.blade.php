<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Therapists</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="bg-gray-50 font-sans">

    {{-- Sidebar --}}
    @include('components.sidebar')


    <div class="lg:ml-64 min-h-screen flex flex-col pb-20 lg:pb-0">

        <main class="flex-1 p-4 sm:p-6">

            {{-- ========================================================= --}}
            {{-- SUCCESS MESSAGE --}}
            {{-- ========================================================= --}}

            @if (session('success'))

                <div
                    class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">

                    <svg
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M5 13l4 4L19 7" />

                    </svg>

                    {{ session('success') }}

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- ERROR MESSAGE --}}
            {{-- ========================================================= --}}

            @if (session('error'))

                <div
                    class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">

                    <svg
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    {{ session('error') }}

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- VALIDATION ERRORS --}}
            {{-- ========================================================= --}}

            @if ($errors->any())

                <div
                    class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">

                    <p class="font-semibold mb-2">
                        Please fix the following errors:
                    </p>

                    <ul class="list-disc list-inside space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- ALPINE ROOT --}}
            {{-- ========================================================= --}}

            <div
                x-data="{

                    showAddModal: false,

                    showEditModal: false,

                    therapistId: null,

                    name: '',
                    email: '',
                    description: '',
                    specialty: '',
                    status: 'available',

                    currentImage: null,


                    openAddModal() {

                        this.showAddModal = true;

                        this.name = '';
                        this.email = '';
                        this.description = '';
                        this.specialty = '';
                        this.status = 'available';

                    },


                    openEditModal(therapist) {

                        this.showEditModal = true;

                        this.therapistId = therapist.id;

                        this.name = therapist.name ?? '';

                        this.email = therapist.email ?? '';

                        this.description =
                            therapist.description ?? '';

                        this.specialty =
                            therapist.specialty ?? '';

                        this.status =
                            therapist.status ?? 'available';


                        this.currentImage = therapist.image

                            ? '{{ asset('storage') }}/' + therapist.image

                            : null;

                    },


                    closeModals() {

                        this.showAddModal = false;

                        this.showEditModal = false;

                    }

                }"

                @keydown.escape.window="closeModals()">


                {{-- ===================================================== --}}
                {{-- PAGE HEADER --}}
                {{-- ===================================================== --}}

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Manage Therapists
                    </h2>

                    <p class="text-sm text-gray-500 mt-0.5">
                        Add, update, and remove therapists.
                    </p>

                </div>


                {{-- ===================================================== --}}
                {{-- SUMMARY CARDS --}}
                {{-- ===================================================== --}}

                <div
                    class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">


                    {{-- Total --}}

                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p
                                class="text-xl sm:text-2xl font-bold text-gray-800">

                                {{ $therapists->total() }}

                            </p>

                            <p
                                class="text-xs text-gray-400 font-medium mt-0.5">

                                Total Therapists

                            </p>

                        </div>

                    </div>


                    {{-- Available --}}

                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p
                                class="text-xl sm:text-2xl font-bold text-gray-800">

                                {{ $therapists->where('status', 'available')->count() }}

                            </p>

                            <p
                                class="text-xs text-gray-400 font-medium mt-0.5">

                                Available

                            </p>

                        </div>

                    </div>


                    {{-- Unavailable --}}

                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p
                                class="text-xl sm:text-2xl font-bold text-gray-800">

                                {{ $therapists->where('status', 'unavailable')->count() }}

                            </p>

                            <p
                                class="text-xs text-gray-400 font-medium mt-0.5">

                                Unavailable

                            </p>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- FILTER --}}
                {{-- ===================================================== --}}

                <div
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                    <form
                        method="GET"
                        action="{{ route('admin.therapists') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">


                        {{-- Search --}}

                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">

                                Search Name

                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by therapist name"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        </div>


                        {{-- Status --}}

                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">

                                Status

                            </label>

                            <select
                                name="status"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    All Status
                                </option>

                                <option
                                    value="available"
                                    {{ request('status') === 'available' ? 'selected' : '' }}>

                                    Available

                                </option>

                                <option
                                    value="unavailable"
                                    {{ request('status') === 'unavailable' ? 'selected' : '' }}>

                                    Unavailable

                                </option>

                            </select>

                        </div>


                        {{-- Specialty --}}

                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">

                                Specialty

                            </label>

                            <input
                                type="text"
                                name="specialty"
                                value="{{ request('specialty') }}"
                                placeholder="Search by specialty"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        </div>


                        {{-- Buttons --}}

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="rounded-lg bg-[#849753] px-6 py-2 text-white hover:bg-[#6F4E37] text-sm w-40">

                                Filter

                            </button>

                            <a
                                href="{{ route('admin.therapists') }}"
                                class="rounded-lg border border-gray-300 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 w-40 text-center">

                                Reset

                            </a>

                        </div>

                    </form>

                </div>


                {{-- ===================================================== --}}
                {{-- THERAPISTS --}}
                {{-- ===================================================== --}}

                <div
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">


                    {{-- Header --}}

                    <div
                        class="flex items-center justify-between mb-4">

                        <h3 class="font-semibold text-gray-800">
                            All Therapists
                        </h3>

                        <button
                            type="button"
                            @click="openAddModal()"
                            class="text-xs font-semibold px-4 py-2.5 rounded-lg bg-[#849753] text-white hover:bg-[#6F4E37] transition">

                            + Add New Therapist

                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- EMPTY --}}
                    {{-- ================================================= --}}

                    @if ($therapists->isEmpty())

                        <div
                            class="py-16 text-center text-gray-400">

                            <svg
                                class="w-12 h-12 mx-auto mb-3 text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24">

                                <path
                                    d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                            </svg>

                            <p class="text-sm">
                                No therapists found.
                            </p>

                        </div>

                    @else


                        {{-- ================================================= --}}
                        {{-- CARDS --}}
                        {{-- ================================================= --}}

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">


                            @foreach ($therapists as $therapist)

                                <div
                                    class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">


                                    {{-- Image --}}

                                    <div
                                        class="relative h-60 bg-gray-100">

                                        @if ($therapist->image)

                                            <img
                                                src="{{ asset('storage/' . $therapist->image) }}"
                                                alt="{{ $therapist->name }}"
                                                class="w-full h-full object-cover">

                                        @else

                                            <div
                                                class="w-full h-full flex items-center justify-center">

                                                <svg
                                                    class="w-16 h-16 text-gray-300"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                                                </svg>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- Card Content --}}

                                    <div class="p-4 sm:p-5">


                                        <div
                                            class="flex items-start justify-between gap-3 mb-3">

                                            <div>

                                                <h4
                                                    class="font-semibold text-gray-800 text-lg">

                                                    {{ $therapist->name }}

                                                </h4>

                                                <p
                                                    class="text-sm text-[#849753] font-medium">

                                                    {{ $therapist->specialty }}

                                                </p>

                                            </div>


                                            @if ($therapist->status === 'available')

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-green-500">
                                                    </span>

                                                    Available

                                                </span>

                                            @else

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-gray-400">
                                                    </span>

                                                    Unavailable

                                                </span>

                                            @endif

                                        </div>


                                        {{-- Description --}}

                                        <p
                                            class="text-sm text-gray-600 mb-4 line-clamp-3">

                                            {{ $therapist->description ?? 'No description available.' }}

                                        </p>


                                        {{-- Actions --}}

                                        <div
                                            class="flex items-center gap-2">


                                            {{-- Modify --}}

                                            <button
                                                type="button"

                                                @click="openEditModal({{ \Illuminate\Support\Js::from([
                                                    'id' => $therapist->id,
                                                    'name' => $therapist->name,
                                                    'email' => $therapist->email,
                                                    'description' => $therapist->description,
                                                    'specialty' => $therapist->specialty,
                                                    'status' => $therapist->status,
                                                    'image' => $therapist->image,
                                                ]) }})"

                                                class="flex-1 text-xs font-semibold px-3 py-2 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition">

                                                Modify

                                            </button>


                                            {{-- Delete --}}

                                            <form
                                                method="POST"
                                                action="{{ route('admin.therapist.destroy', $therapist) }}"
                                                class="flex-1">

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"

                                                    onclick="return confirm('Delete {{ addslashes($therapist->name) }}? This cannot be undone.')"

                                                    class="w-full text-xs font-semibold px-3 py-2 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">

                                                    Delete

                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        {{-- Pagination --}}

                        @if ($therapists->hasPages())

                            <div class="mt-5">

                                {{ $therapists->links() }}

                            </div>

                        @endif

                    @endif

                </div>


                {{-- ===================================================== --}}
                {{-- ADD THERAPIST MODAL --}}
                {{-- ===================================================== --}}

                <div
                    x-show="showAddModal"
                    x-transition.opacity
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4">


                    {{-- Overlay --}}

                    <div
                        class="absolute inset-0 bg-black/50"
                        @click="showAddModal = false">
                    </div>


                    {{-- Modal --}}

                    <div
                        x-show="showAddModal"
                        x-transition

                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto"

                        @click.stop>


                        {{-- Header --}}

                        <div
                            class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                            <div>

                                <h3
                                    class="text-lg font-semibold text-gray-800">

                                    Add New Therapist

                                </h3>

                                <p
                                    class="text-xs text-gray-400 mt-1">

                                    Create a new therapist profile with login credentials.

                                </p>

                            </div>

                            <button
                                type="button"
                                @click="showAddModal = false"
                                class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">

                                &times;

                            </button>

                        </div>


                        {{-- Add Form --}}

                        <form
                            method="POST"
                            action="{{ route('admin.therapist.store') }}"
                            enctype="multipart/form-data"
                            class="p-6 space-y-5">

                            @csrf


                            {{-- Name + Email --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Name

                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. Jane Doe"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Email

                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="e.g. jane@example.com"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>

                            </div>


                            {{-- Password --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Password

                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        placeholder="Min 8 characters"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Confirm Password

                                    </label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Re-enter password"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>

                            </div>


                            {{-- Specialty + Status --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Specialty

                                    </label>

                                    <input
                                        type="text"
                                        name="specialty"
                                        value="{{ old('specialty') }}"
                                        placeholder="e.g. Couples Therapy"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Status

                                    </label>

                                    <select
                                        name="status"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm">

                                        <option value="available">
                                            Available
                                        </option>

                                        <option value="unavailable">
                                            Unavailable
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Description --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700">

                                    Description

                                </label>

                                <textarea
                                    name="description"
                                    rows="4"
                                    placeholder="Short therapist description"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">{{ old('description') }}</textarea>

                            </div>


                            {{-- Image --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700">

                                    Profile Image

                                </label>

                                <input
                                    type="file"
                                    name="image"
                                    accept="image/*"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm">

                                <p
                                    class="mt-1 text-xs text-gray-500">

                                    JPG, JPEG, PNG. Max 2MB.

                                </p>

                            </div>


                            {{-- Buttons --}}

                            <div
                                class="flex items-center justify-end gap-3 pt-2">

                                <button
                                    type="button"
                                    @click="showAddModal = false"
                                    class="rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">

                                    Cancel

                                </button>

                                <button
                                    type="submit"
                                    class="rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6F4E37] transition">

                                    Save Therapist

                                </button>

                            </div>

                        </form>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- MODIFY THERAPIST MODAL --}}
                {{-- ===================================================== --}}

                <div
                    x-show="showEditModal"
                    x-transition.opacity
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4">


                    {{-- Overlay --}}

                    <div
                        class="absolute inset-0 bg-black/50"
                        @click="showEditModal = false">
                    </div>


                    {{-- Modal --}}

                    <div
                        x-show="showEditModal"
                        x-transition

                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto"

                        @click.stop>


                        {{-- Header --}}

                        <div
                            class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                            <div>

                                <h3
                                    class="text-lg font-semibold text-gray-800">

                                    Modify Therapist

                                </h3>

                                <p
                                    class="text-xs text-gray-400 mt-1">

                                    Update the therapist information and login credentials.

                                </p>

                            </div>

                            <button
                                type="button"
                                @click="showEditModal = false"
                                class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">

                                &times;

                            </button>

                        </div>


                        {{-- Edit Form --}}

                        <form
                            :action="'{{ url('/admin/therapists') }}/' + therapistId"
                            method="POST"
                            enctype="multipart/form-data"
                            class="p-6 space-y-5">

                            @csrf

                            @method('PUT')


                            {{-- Current Image --}}

                            <div class="flex justify-center">

                                <div class="text-center">

                                    <p
                                        class="text-sm font-medium text-gray-700 mb-3">

                                        Current Profile Picture

                                    </p>


                                    <template x-if="currentImage">

                                        <img
                                            :src="currentImage"
                                            alt="Therapist"
                                            class="w-32 h-32 rounded-2xl object-cover border border-gray-200 shadow-sm mx-auto">

                                    </template>


                                    <template x-if="!currentImage">

                                        <div
                                            class="w-32 h-32 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto">

                                            <svg
                                                class="w-12 h-12 text-gray-300"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                viewBox="0 0 24 24">

                                                <path
                                                    d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                                            </svg>

                                        </div>

                                    </template>

                                </div>

                            </div>


                            {{-- Name + Email --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Name

                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        x-model="name"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Email

                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        x-model="email"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>

                            </div>


                            {{-- Password --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        New Password (optional)

                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        placeholder="Leave empty to keep current"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Confirm New Password

                                    </label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Re-enter new password"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>

                            </div>


                            {{-- Specialty + Status --}}

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Specialty

                                    </label>

                                    <input
                                        type="text"
                                        name="specialty"
                                        x-model="specialty"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20">

                                </div>


                                <div>

                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700">

                                        Status

                                    </label>

                                    <select
                                        name="status"
                                        x-model="status"
                                        required
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm">

                                        <option value="available">
                                            Available
                                        </option>

                                        <option value="unavailable">
                                            Unavailable
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Description --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700">

                                    Description

                                </label>

                                <textarea
                                    name="description"
                                    x-model="description"
                                    rows="4"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-[#849753]/20"></textarea>

                            </div>


                            {{-- Change Image --}}

                            <div>

                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700">

                                    Change Profile Image

                                </label>

                                <input
                                    type="file"
                                    name="image"
                                    accept="image/*"
                                    class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm">

                                <p
                                    class="mt-1 text-xs text-gray-500">

                                    Leave empty to keep the current picture.

                                </p>

                            </div>


                            {{-- Buttons --}}

                            <div
                                class="flex items-center justify-end gap-3 pt-2">

                                <button
                                    type="button"
                                    @click="showEditModal = false"
                                    class="rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">

                                    Cancel

                                </button>

                                <button
                                    type="submit"
                                    class="rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6F4E37] transition">

                                    Update Therapist

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

