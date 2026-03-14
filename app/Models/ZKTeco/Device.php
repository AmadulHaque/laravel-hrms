<?php

namespace App\Models\ZKTeco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'serial_number',
        'area_id',
        'device_ip',
        'user_total_qty',
        'command_total_count',
        'heartbeat_status_at',
        'request_heartbeat_seconds',
        'created_by',
        'updated_at',
        'created_at',
    ];

    protected $appends = ['heartbeat_status'];

    protected $casts = [
        'status' => 'integer',
        'user_total_qty' => 'integer',
        'command_total_count' => 'integer',
        'request_heartbeat_seconds' => 'integer',
        'heartbeat_status_at' => 'datetime',
    ];

    public function getHeartbeatStatusAttribute(): bool
    {
        if (! $this->heartbeat_status_at) {
            return false;
        }

        $threshold = $this->request_heartbeat_seconds ?? 30;

        return $this->heartbeat_status_at->diffInSeconds(now()) <= $threshold;
    }
}
