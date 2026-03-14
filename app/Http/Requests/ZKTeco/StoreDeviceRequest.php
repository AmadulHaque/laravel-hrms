<?php

namespace App\Http\Requests\ZKTeco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && $user->can('manage-biomatric-attedance-settings');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('devices', 'serial_number')->where(function ($query) {
                    return $query->whereIn('created_by', getCompanyAndUsersId());
                }),
            ],
            'area_id' => ['nullable', 'string', 'max:255'],
            'device_ip' => ['nullable', 'ip'],
            'request_heartbeat_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
            'status' => ['required', 'integer', Rule::in([0, 1])],
        ];
    }
}
