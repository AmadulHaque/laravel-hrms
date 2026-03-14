<?php

namespace App\Models\ZKTeco;

use App\Cache\ZKTecoCache;
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
        'created_by',
        'updated_at',
        'created_at',
    ];

    protected $appends = ['heartbeat_status'];

    protected $casts = [
        'status' => 'integer',
        'user_total_qty' => 'integer',
        'command_total_count' => 'integer',
        'heartbeat_status_at' => 'datetime',
    ];

    public function getHeartbeatStatusAttribute(): bool
    {
        $heartbeatStatusAt = ZKTecoCache::isOnline($this->serial_number);
        return !is_null($heartbeatStatusAt) && $heartbeatStatusAt->diffInSeconds(now()) <= ZKTecoCache::CACHE_DEVICE_TTL;
    }
}
