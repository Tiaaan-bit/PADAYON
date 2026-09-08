@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Add-Ons')

@section('content')


    <div class=" min-h-screen flex flex-col ">

        <div class="flex-1 p-4 sm:p-6">

            {{-- Success --}}
            @if (session('success'))
                <div
                    class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">

                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M5 13l4 4L19 7" />

                    </svg>

                    {{ session('success') }}

                </div>
            @endif


            {{-- Error --}}
            @if (session('error'))
                <div
                    class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">

                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    {{ session('error') }}

                </div>
            @endif


            {{-- Validation Errors --}}
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
            <div x-data="{
                showModal: false,
                editing: false,
            
                addOnId: null,
                name: '',
                price: '',
                duration_minutes: '',
                status: 'active',
            
                openAddModal() {
            
                    this.showModal = true;
                    this.editing = false;
            
                    this.addOnId = null;
                    this.name = '';
                    this.price = '';
                    this.duration_minutes = '';
                    this.status = 'active';
                },
            
                openEditModal(addOn) {
            
                    this.showModal = true;
                    this.editing = true;
            
                    this.addOnId = addOn.id;
                    this.name = addOn.name ?? '';
                    this.price = addOn.price ?? '';
                    this.duration_minutes = addOn.duration_minutes ?? '';
                    this.status = addOn.status ?? 'active';
                },
            
                closeModal() {
            
                    this.showModal = false;
                    this.editing = false;
            
                    this.addOnId = null;
                    this.name = '';
                    this.price = '';
                    this.duration_minutes = '';
                    this.status = 'active';
                }
            }">

                {{-- Page Header --}}
                <div class="mb-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Manage Add Ons
                    </h2>

                    <p class="text-sm text-gray-500 mt-0.5">
                        Add, update, and remove appointment add-ons.
                    </p>

                </div>


                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

                    {{-- Total --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $addOns->total() }}
                            </p>

                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Total Add Ons
                            </p>

                        </div>

                    </div>


                    {{-- Active --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $totalActiveAddOns ?? $addOns->where('status', 'active')->count() }}
                            </p>

                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Active
                            </p>

                        </div>

                    </div>


                    {{-- Inactive --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                        <div class="text-center">

                            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                                {{ $totalInactiveAddOns ?? $addOns->where('status', 'inactive')->count() }}
                            </p>

                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                Inactive
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Search / Filter --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                    <form method="GET" action="{{ route('admin.addons') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <div class="md:col-span-3">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Search
                            </label>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by add-on name"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        </div>


                        <div class="flex items-center gap-3">

                            <button type="submit"
                                class="rounded-lg bg-[#849753] px-6 py-2 text-white hover:bg-[#6F4E37] text-sm w-40">
                                Filter
                            </button>

                            <a href="{{ route('admin.addons') }}"
                                class="rounded-lg border border-gray-300 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 w-40 text-center">
                                Reset
                            </a>

                        </div>

                    </form>

                </div>


                {{-- All Add Ons --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

                        <h3 class="font-semibold text-gray-800">
                            All Add Ons
                        </h3>

                        <button type="button" @click="openAddModal()"
                            class="text-xs font-semibold px-3 py-2 rounded-lg bg-[#849753] text-white hover:bg-[#6F4E37] transition">
                            Add New Add On
                        </button>

                    </div>


                    @if ($addOns->isEmpty())

                        <div class="py-16 text-center text-gray-400">

                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">

                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                            </svg>

                            <p class="text-sm">
                                No add ons found.
                            </p>

                        </div>
                    @else
                        <div class="overflow-x-auto">

                            <table class="w-full text-sm">

                                <thead>

                                    <tr class="bg-gray-50 text-left">

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            ID
                                        </th>

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Add On Name
                                        </th>

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Duration
                                        </th>

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Price
                                        </th>

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Status
                                        </th>

                                        <th
                                            class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-gray-100">

                                    @foreach ($addOns as $addOn)
                                        <tr class="hover:bg-gray-50 transition">

                                            {{-- ID --}}
                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                                {{ $addOn->id }}
                                            </td>


                                            {{-- Name --}}
                                            <td class="px-5 py-4">

                                                <p class="font-semibold text-gray-800 whitespace-nowrap">
                                                    {{ $addOn->name }}
                                                </p>

                                            </td>


                                            {{-- Duration --}}
                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                                {{ $addOn->duration_minutes }} Minutes

                                            </td>


                                            {{-- Price --}}
                                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                                ₱{{ number_format((float) $addOn->price, 2) }}

                                            </td>


                                            {{-- Status --}}
                                            <td class="px-5 py-4">

                                                @if ($addOn->status === 'active')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                        Active

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>

                                                        Inactive

                                                    </span>
                                                @endif

                                            </td>


                                            {{-- Actions --}}
                                            <td class="px-5 py-4">

                                                <div class="flex items-center gap-2">

                                                    {{-- Modify --}}
                                                    <button type="button"
                                                        @click="openEditModal({{ \Illuminate\Support\Js::from([
                                                            'id' => $addOn->id,
                                                            'name' => $addOn->name,
                                                            'price' => $addOn->price,
                                                            'duration_minutes' => $addOn->duration_minutes,
                                                            'status' => $addOn->status,
                                                        ]) }})"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition whitespace-nowrap">
                                                        Modify
                                                    </button>


                                                    {{-- Delete --}}
                                                    <form method="POST"
                                                        action="{{ route('admin.addons.destroy', $addOn) }}">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            onclick="return confirm('Delete {{ addslashes($addOn->name) }}? This cannot be undone.')"
                                                            class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition whitespace-nowrap">
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


                        {{-- Pagination --}}
                        @if ($addOns->hasPages())
                            <div class="px-5 py-4 border-t border-gray-100 flex justify-end">

                                {{ $addOns->links() }}

                            </div>
                        @endif

                    @endif

                </div>


                {{-- ===================================================== --}}
                {{-- ADD / EDIT MODAL --}}
                {{-- ===================================================== --}}

                <div x-show="showModal" x-transition.opacity style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4">

                    {{-- Background --}}
                    <div class="absolute inset-0 bg-black/50" @click="closeModal()"></div>


                    {{-- Modal --}}
                    <div x-show="showModal" x-transition @click.stop
                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">

                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">

                            <div>

                                <h3 class="text-lg font-bold text-gray-800"
                                    x-text="editing ? 'Modify Add On' : 'Add New Add On'"></h3>

                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span x-show="!editing">
                                        Create a new appointment add-on.
                                    </span>

                                    <span x-show="editing">
                                        Update this appointment add-on.
                                    </span>
                                </p>

                            </div>


                            {{-- Close --}}
                            <button type="button" @click="closeModal()"
                                class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">

                                    <path d="M6 18L18 6M6 6l12 12" />

                                </svg>

                            </button>

                        </div>


                        {{-- Modal Body --}}
                        <form
                            :action="editing
                                ?
                                '{{ url('/admin/addons') }}/' + addOnId :
                                '{{ route('admin.addons.store') }}'"
                            method="POST" class="p-6 space-y-5">

                            @csrf


                            {{-- PUT for Edit --}}
                            <template x-if="editing">

                                <input type="hidden" name="_method" value="PUT">

                            </template>


                            {{-- Name + Duration --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- Name --}}
                                <div>

                                    <label for="modal_name" class="mb-2 block text-sm font-medium text-gray-700">
                                        Add On Name
                                    </label>

                                    <input type="text" id="modal_name" name="name" x-model="name"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                        placeholder="e.g. Hot Stone">

                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                {{-- Duration --}}
                                <div>

                                    <label for="modal_duration_minutes"
                                        class="mb-2 block text-sm font-medium text-gray-700">
                                        Duration Minutes
                                    </label>

                                    <input type="number" id="modal_duration_minutes" name="duration_minutes"
                                        x-model="duration_minutes"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                        placeholder="Enter duration minutes">

                                    @error('duration_minutes')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                            </div>


                            {{-- Price + Status --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- Price --}}
                                <div>

                                    <label for="modal_price" class="mb-2 block text-sm font-medium text-gray-700">
                                        Price
                                    </label>

                                    <input type="number" id="modal_price" name="price" step="0.01"
                                        x-model="price"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20"
                                        placeholder="Enter price">

                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                {{-- Status --}}
                                <div>

                                    <label for="modal_status" class="mb-2 block text-sm font-medium text-gray-700">
                                        Status
                                    </label>

                                    <select id="modal_status" name="status" x-model="status"
                                        class="w-full rounded-xl border border-[#849753] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#849753] focus:ring-4 focus:ring-[#849753]/20">

                                        <option value="active">
                                            Active
                                        </option>

                                        <option value="inactive">
                                            Inactive
                                        </option>

                                    </select>

                                    @error('status')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                            </div>


                            {{-- Modal Buttons --}}
                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">

                                <button type="button" @click="closeModal()"
                                    class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-300">
                                    Cancel
                                </button>


                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6F4E37]">

                                    <span x-text="editing ? 'Update Add On' : 'Save Add On'"></span>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
