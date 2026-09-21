<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AddOn\CreateAddOn;
use App\Actions\Admin\AddOn\DeleteAddOn;
use App\Actions\Admin\AddOn\UpdateAddOn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddOnRequest;
use App\Models\AddOns;
use App\Repositories\Admin\AddOn\AddOnRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminAddonsController extends Controller
{
    public function addons(Request $request, AddOnRepositoryInterface $addOns): View
    {
        $addOnList = $addOns->getAll($request->input('search'));

        return view('admin.addons', [
            'addOns' => $addOnList,
        ]);
    }

    public function store(AddOnRequest $request, CreateAddOn $createAddOn): RedirectResponse
    {
        Gate::authorize('create', AddOns::class);

        $createAddOn->execute($request->validated());

        return redirect()->route('admin.addons')->with('success', 'Add-on added successfully.');
    }

    public function update(AddOnRequest $request, AddOns $addOn, UpdateAddOn $updateAddOn): RedirectResponse
    {
        Gate::authorize('update', $addOn);

        $updateAddOn->execute($addOn, $request->validated());

        return redirect()->route('admin.addons')->with('success', 'Add-on updated successfully.');
    }

    public function destroy(AddOns $addOn, DeleteAddOn $deleteAddOn): RedirectResponse
    {
        Gate::authorize('delete', $addOn);

        $deleteAddOn->execute($addOn);

        return redirect()->route('admin.addons')->with('success', 'Add-on deleted successfully.');
    }
}
