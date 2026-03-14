<?php

namespace App\Services\ZKTeco;

use App\Models\ZKTeco\Device;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class DeviceService
{
    public function create(array $data): Device
    {
        $data['created_by'] = Auth::id();

        return Device::create($data);
    }

    public function update(Device $device, array $data): Device
    {
        $this->ensureCompanyAccess($device);
        $device->update($data);

        return $device->refresh();
    }

    public function delete(Device $device): void
    {
        $this->ensureCompanyAccess($device);
        $device->delete();
    }

    protected function ensureCompanyAccess(Device $device): void
    {
        if (! in_array($device->created_by, getCompanyAndUsersId(), true)) {
            throw new AuthorizationException(__('Permission denied.'));
        }
    }

}
