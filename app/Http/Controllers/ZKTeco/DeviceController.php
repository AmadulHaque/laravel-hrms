<?php

namespace App\Http\Controllers\ZKTeco;

use App\Http\Controllers\Controller;
use App\Http\Requests\ZKTeco\StoreDeviceRequest;
use App\Http\Requests\ZKTeco\UpdateDeviceRequest;
use App\Models\ZKTeco\Device;
use App\Services\ZKTeco\DeviceService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $deviceService
    ) {
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        try {
            $this->deviceService->create($request->validated());

            return redirect()->back()->with('success', __('Device added successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
        }
    }

    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        try {
            $this->deviceService->update($device, $request->validated());

            return redirect()->back()->with('success', __('Device updated successfully.'));
        } catch (AuthorizationException $th) {
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
        }
    }

    public function destroy(Device $device): RedirectResponse
    {
        try {
            $this->deviceService->delete($device);

            return redirect()->back()->with('success', __('Device deleted successfully.'));
        } catch (AuthorizationException $th) {
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
        }
    }
}
