<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Service\CreateService;
use App\Actions\Admin\Service\DeleteService;
use App\Actions\Admin\Service\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Services;
use App\Repositories\Admin\Service\ServiceRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminServicesController extends Controller
{
    public function services(Request $request, ServiceRepositoryInterface $services): View
    {
        $serviceList = $services->getAll($request->input('search'));

        $totalActiveServices = $services->getActiveCount();

        $totalInactiveServices = $services->getInactiveCount();

        return view('admin.services', [
            'services' => $serviceList,
            'totalActiveServices' => $totalActiveServices,
            'totalInactiveServices' => $totalInactiveServices,
        ]);
    }

    public function create(): View
    {
        return view('admin.services');
    }

    public function store(ServiceRequest $request, CreateService $createService): RedirectResponse
    {
        Gate::authorize('create', Services::class);

        $createService->execute($request->validated());

        return redirect()->route('admin.services')->with('success', 'Service added successfully.');
    }

    public function edit(Services $service, ServiceRepositoryInterface $services): View
    {
        $serviceList = $services->getAll();

        return view('admin.services', [
            'services' => $serviceList,
            'service' => $service,
        ]);
    }

    public function update(ServiceRequest $request, Services $service, UpdateService $updateService): RedirectResponse
    {
        Gate::authorize('update', $service);

        $updateService->execute($service, $request->validated());

        return redirect()->route('admin.services')->with('success', 'Service updated successfully.');
    }

    public function destroy(Services $service, DeleteService $deleteService): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $deleteService->execute($service);

        return redirect()->route('admin.services')->with('success', 'Service deleted successfully.');
    }
}
