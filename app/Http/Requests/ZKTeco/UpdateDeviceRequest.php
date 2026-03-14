<?php

namespace App\Http\Requests\ZKTeco;

use App\Models\ZKTeco\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        $device = $this->route('device');

        if (! $user || ! $user->can('manage-biomatric-attedance-settings')) {
            return false;
        }

        if ($device instanceof Device) {
            return in_array($device->created_by, getCompanyAndUsersId(), true);
        }

        return true;
    }

    public function rules(): array
    {
        $device = $this->route('device');
        $deviceId = $device instanceof Device ? $device->id : $device;

        return [
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('devices', 'serial_number')
                    ->where(function ($query) {
                        return $query->whereIn('created_by', getCompanyAndUsersId());
                    })
                    ->ignore($deviceId),
            ],
            'area_id' => ['nullable', 'string', 'max:255'],
            'device_ip' => ['nullable', 'ip'],
            'request_heartbeat_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
            'status' => ['required', 'integer', Rule::in([0, 1])],
        ];
    }
}
